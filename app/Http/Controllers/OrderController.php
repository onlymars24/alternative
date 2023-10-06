<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Setting;
use App\Enums\FermaEnum;
use App\Models\Passenger;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\FermaService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function book(Request $request){
        $body = json_encode($request->sale);
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->withBody($body, 'application/json')->post(env('AVTO_SERVICE_URL').'/order/book/'.$request->uid);
        $order = json_decode($order_json);
        if(!isset($order->id)){
            return response([
                'error' => $order
            ], 422);
        }
        $orderFromDB = Order::create([
            'id' => $order->id,
            'order_info' => $order_json,
            'user_id' => Auth::id(),
        ]);
        $orderBundle = [
            'agent' => ['agentType' => 7],
            'cartItems' => [
                'items' => []
            ]
        ];
        $lastKey = 0;
        foreach($order->tickets as $key => $ticket){
            $ticketNew = (array)$ticket;
            $ticketNew['order_id'] = $order->id;
            $orderBundleEl = [ "positionId"=> $key+1,
                "name" => 'Бил'.(!empty($ticketNew['ticketNum']) ? ' №' : '').' '.$ticketNew['ticketNum'].' '.$ticketNew['dispatchDate'].' Мст№'.$ticketNew['seat'].' '.$ticketNew['lastName'].' '.mb_substr($ticketNew['firstName'], 0, 1).'. '.mb_substr($ticketNew['middleName'], 0, 1).'.',
                "quantity" => [ "value"=> 1, "measure" => 0 ],
                "itemCode" => "NM-".($key+1),
                "tax"=> ["taxType"=> 0, "taxSum"=> 0],
                "itemPrice"=> $ticketNew['price'] * 100
            ];

            $ticketNew['orderBundle'] = json_encode($orderBundleEl);
            Ticket::create(
                $ticketNew
            );
            $orderBundle['cartItems']['items'][] = $orderBundleEl;
            $lastKey = $key+1;
        }
        $duePercent = json_decode(Setting::where('name', 'dues')->first()->data);
        $duePercent = (array)$duePercent;
        $duePercent = $duePercent['clusterDue'];
        $duePrice = ceil($order->total * $duePercent / 100);
        $orderFromDB->duePercent = $duePercent;
        $orderFromDB->duePrice = $duePrice;
        $orderBundle['cartItems']['items'][] = [
            "positionId"=> $lastKey+1,
            "name" => 'Сервисный сбор',
            "quantity" => [ "value"=> 1, "measure" => 0 ],
            "itemCode" => "NM-".($lastKey+1),
            "tax"=> ["taxType"=> 0, "taxSum"=> 0],
            "itemPrice"=> $duePrice * 100
        ];

        foreach($request->sale as $el){
            if(!$el['saved']){
                $passenger = Passenger::create([
                    'name' => $el['firstName'],
                    'surname' => $el['lastName'],
                    'patronymic' => $el['middleName'],
                    'birth_date' => $el['birthday'],
                    'citizenship' => $el['citizenship'],
                    'doc_number' => $el['docNum'],
                    'doc_series' => $el['docSeries'],
                    'doc_type' => $el['docTypeName'],
                    'ticket_type' => $el['ticketTypeName'],
                    'user_id' => Auth::id()
                ]);
            }
        }
        //begin createPayment
        $data = [
            'userName' => config('services.payment.userName'),
            'password' => config('services.payment.password'),
            'orderNumber' => $order->id,
            'amount' => ($order->total + $duePrice) * 100,
            'orderBundle' => json_encode($orderBundle),
            'returnUrl' => env('FRONTEND_URL').'/account',
            'dynamicCallbackUrl' => env('BACKEND_URL').'/order/confirm/'
        ];
        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/register.do', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $payment = curl_exec($curl); // Выполняем запрос
        Log::info('payment: '.$payment);
        $payment = json_decode($payment);
        if(!isset($payment->orderId)){
            foreach($orderFromDB->tickets as $ticket){
                $ticket->delete();
            }
            $orderFromDB->delete();
            return response([
                'error' => $payment
            ], 422);
        }
        $orderFromDB->bankOrderId = $payment->orderId;
        $orderFromDB->formUrl = $payment->formUrl;
        $orderFromDB->save();
        curl_close($curl); // Закрываем соединение

        return response([
            'order' => $order,
            'payment' => $payment
        ]);  
    }

    public function confirm(Request $request){
        if(empty($request->orderNumber) || $request->operation != 'deposited'){
            return;
        }
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->post(env('AVTO_SERVICE_URL').'/order/confirm/'.$request->orderNumber.'/По банковской карте');
        Log::info('obj_json: '.$order_json);
        $order_obj = json_decode($order_json);
        if(!isset($order_obj->id)){
            return;
        }

        $transaction = Transaction::create([
            'StatusCode' => 0,
            'type' => 'Income',
            'order_id' => $request->orderNumber
        ]);
        $body = FermaEnum::$body;
        $item = FermaEnum::$item;
        $percent = FermaEnum::$percent;
        $body['Request']['Type'] = 'Income';
        $body['Request']['InvoiceId'] = $transaction->id;
        foreach($order_obj->tickets as $ticket){
            $ticketFromDB = Ticket::find($ticket->id);
            $ticketFromDB->update((array)$ticket);
            $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket->hash.'.pdf';
            $file_name = basename($url);
            file_put_contents('tickets/'.$file_name, file_get_contents($url));

            $item['Label'] = 'Бил'.(!empty($ticket->ticketNum) ? ' №' : '').$ticket->ticketNum.' '.$ticket->dispatchDate.' Мст№'.$ticket->seat.' '.$ticket->lastName.' '.mb_substr($ticket->firstName, 0, 1).'. '.mb_substr($ticket->middleName, 0, 1).'.';
            $item['Price'] = $ticket->price;
            $item['Amount'] = $ticket->price;
            $body['Request']['CustomerReceipt']['Items'][] = $item;
            $ticketFromDB->customerItem = json_encode($item);
            $ticketFromDB->save();
        }

        $order = Order::find($request->orderNumber);
        $order->order_info = $order_json;

        $percent['Price'] = $percent['Amount'] = $order->duePrice;

        $body['Request']['CustomerReceipt']['Items'][] = $percent;
        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total + $order->duePrice;
        Log::info('Body: '.json_encode($body));
        $ReceiptId = FermaService::receipt($body);
        Log::info('Receipt: '.$ReceiptId);
        $ReceiptId = json_decode($ReceiptId);
        $ReceiptId = $ReceiptId->Data->ReceiptId;
        $receipt = FermaService::getStatus($ReceiptId);
        Log::info('Receipt: '.$receipt);
        $receipt = json_decode($receipt);

        $transaction->StatusCode = $receipt->Data->StatusCode;
        $transaction->ReceiptId = $receipt->Data->ReceiptId;
        if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
            $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
        }
        $transaction->save();
        $data = [
            'userName' => config('services.payment.userName'),
            'password' => config('services.payment.password'),
            'orderId' => $request->mdOrder
        ];
        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/getOrderStatus.do', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $orderFromBank = curl_exec($curl); // Выполняем запрос
        curl_close($curl); // Закрываем соединение

        $orderFromBank = json_decode($orderFromBank);

        $order->ip = $orderFromBank->Ip;
        $order->pan = $orderFromBank->Pan;
        $order->save();

        Log::info('Order\'s confirmed'.$request->orderNumber.' '.$request->mdOrder);
    }

    public function getBack(Request $request){
        //найти заказ
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/order/'.$request->orderId);
        $order = json_decode($order_json);
        $orderFromDB = Order::find($request->orderId);
        $tickets = $orderFromDB->tickets->where('status', 'S');


        //перебрать невозвращённые билеты: для каждого вызвать возврат, обновить бд билета, засунуть в массив для экваринга, засунуть в массив для чека, обновить файл билета
        if($tickets->count() == 0){
            return response([
                'errorMessage' => 'Все билеты возвращены'
            ]);
        }
        foreach($tickets as $ticket){
            
        }
        return response([
            'tickets' => $tickets->count()
        ]);
        
        //проверить рейс на отмену и если что добавить комиссию
        
        //обновить бд заказа
        
        //выполнять возврат по экварингу

        //распечатать чек
    }

    public function all(){
        $user = Auth::user();
        $orders = $user->orders()->orderByDesc('created_at')->get();
        return response([
            'orders' => $orders
        ]);
    }

    public function one(Request $request){
        $order = Order::find($request->order_id);
        return response([
            'order' => $order
        ]);
    }
}