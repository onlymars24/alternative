<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
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
        ])->withBody($body, 'application/json')->post('https://cluster.avtovokzal.ru/gdstest/rest/order/book/'.$request->uid);
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
        foreach($order->tickets as $key => $ticket){
            $ticketNew = (array)$ticket;
            $ticketNew['order_id'] = $order->id;
            $orderBundleEl = [ "positionId"=> $key+1,
                "name" => 'Бил'.!empty($ticketNew['ticketNum']) ? ' №' : ''.$ticketNew['ticketNum'].' '.$ticketNew['ticketNum'].' '.$ticketNew['dispatchDate'].' Мст№'.$ticketNew['seat'].' '.$ticketNew['lastName'].' '.mb_substr($ticketNew['firstName'], 0, 1).'. '.mb_substr($ticketNew['middleName'], 0, 1).'.',
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
        }
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
            'amount' => $order->total * 100,
            'returnUrl' => env('FRONTEND_URL').'/account',
            'dynamicCallbackUrl' => env('BACKEND_URL').'/order/confirm/'
        ];
        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => 'https://alfa.rbsuat.com/payment/rest/register.do', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $payment = curl_exec($curl); // Выполняем запрос
        $payment = json_decode($payment);

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
        ])->post('https://cluster.avtovokzal.ru/gdstest/rest/order/confirm/'.$request->orderNumber.'/По банковской карте');
        Log::info('obj_json: '.$order_json);
        $order_obj = json_decode($order_json);
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
            
            $url = 'https://cluster.avtovokzal.ru/gdstest/mvc/download/'.$ticket->hash.'.pdf';
            $file_name = basename($url);
            file_put_contents('tickets/'.$file_name, file_get_contents($url));
            //start

            $item['Label'] = 'Бил'.(!empty($ticket->ticketNum) ? ' №' : '').$ticket->ticketNum.' '.$ticket->dispatchDate.' Мст№'.$ticket->seat.' '.$ticket->lastName.' '.mb_substr($ticket->firstName, 0, 1).'. '.mb_substr($ticket->middleName, 0, 1).'.';
            $item['Price'] = $ticket->price;
            $item['Amount'] = $ticket->price;
            $body['Request']['CustomerReceipt']['Items'][] = $item;
            $ticketFromDB->customerItem = json_encode($item);
            $ticketFromDB->save();
            //end
        }

        $order = Order::find($request->orderNumber);
        $order->order_info = $order_json;

        $percent['Price'] = $percent['Amount'] = $order_obj->agent->extra == null ? 0 : $order_obj->agent->extra;

        $body['Request']['CustomerReceipt']['Items'][] = $percent;
        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total;
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
            CURLOPT_URL => 'https://alfa.rbsuat.com/payment/rest/getOrderStatus.do', 
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

    // public function confirm($order_id){
    //     $order_json = Http::withHeaders([
    //         'Authorization' => env('AVTO_SERVICE_KEY'),
    //     ])->post('https://cluster.avtovokzal.ru/gdstest/rest/order/confirm/'.$order_id.'/По банковской карте');
    //     $order_obj = json_decode($order_json);

    //     foreach($order_obj->tickets as $ticket){
    //         $ticketFromDB = Ticket::find($ticket->id);
    //         $ticketFromDB->update((array)$ticket);
    //         $url = 'https://cluster.avtovokzal.ru/gdstest/mvc/download/'.$ticket->hash.'.pdf';
    //         $file_name = basename($url);
    //         file_put_contents('tickets/'.$file_name, file_get_contents($url));
    //     }
    //     $order = Order::find($order_id);
    //     $order->order_info = $order_json;
    //     $order->save();
    //     return redirect('http://localhost:5173/account');
    //     // return response([
    //     //     'order' => $order_obj
    //     // ]);
    // }

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