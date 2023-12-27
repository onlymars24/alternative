<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Setting;
use App\Enums\FermaEnum;
use App\Models\Passenger;
use App\Models\Transaction;
use App\Enums\InsuranceEnum;
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

        $race_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$request->uid);
        Log::info('race_json: '.$race_json);
        $race = json_decode($race_json);
        // timezone saving
        $timezone = $race->depot->timezone;


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

        $duePercent = json_decode(Setting::where('name', 'dues')->first()->data);
        $duePercent = (array)$duePercent;
        $duePercent = $duePercent['clusterDue'];
        $duePrice = 0;

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
            $ticketFromDB = Ticket::create(
                $ticketNew
            );
            $ticketFromDB->duePercent = $duePercent;
            $ticketFromDB->duePrice = ceil($ticketFromDB->price * $duePercent / 100);
            
            // timezone saving 
            $ticketFromDB->timezone = $timezone;
            $ticketFromDB->save();
            $duePrice += $ticketFromDB->duePrice;
            $orderBundle['cartItems']['items'][] = $orderBundleEl;
            $lastKey = $key+1;
        }

        $orderFromDB->duePercent = $duePercent;
        $orderFromDB->duePrice = $duePrice;
        $orderFromDB->timezone = $timezone;

        //insurance info
        if($request->insured){
            $orderFromDB->insured = $request->insured;
            $orderBundle['cartItems']['items'][] = [
                "positionId"=> $lastKey+1,
                "name" => 'Страховка',
                "quantity" => [ "value"=> 1, "measure" => 0 ],
                "itemCode" => "NM-".($lastKey+1),
                "tax"=> ["taxType"=> 0, "taxSum"=> 0],
                "itemPrice"=> $request->insurancePrice * 100
            ];
        }
        $orderBundle['cartItems']['items'][] = [
            "positionId"=> $lastKey+2,
            "name" => 'Сервисный сбор',
            "quantity" => [ "value"=> 1, "measure" => 0 ],
            "itemCode" => "NM-".($lastKey+2),
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

        if($request->insured){
            $data['amount'] = ($order->total + $duePrice + $request->insurancePrice) * 100;
        }
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

        
        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total + $order->duePrice;
        $user = $order->user;
        if($user->email){
            $body['Request']['CustomerReceipt']['Email'] = $user->email;
        }


        //insurance check
        if($order->insured){
            $policiesTotalRate = 0;
            foreach($order_obj->tickets as $ticket){
                $ticketFromDB = Ticket::find($ticket->id);
                $insuranceBody = InsuranceEnum::$body;
                $insuranceBody['segments'][0]['departure']['date'] = date('Y-m-d\TH:i', strtotime($ticketFromDB->dispatchDate));
                $insuranceBody['segments'][0]['departure']['point'] = $ticketFromDB->dispatchStation;
                $insuranceBody['segments'][0]['arrival']['date'] = date('Y-m-d\TH:i', strtotime($ticketFromDB->arrivalDate));
                $insuranceBody['segments'][0]['arrival']['point'] = $ticketFromDB->arrivalStation;
                // Log::info('arrivalStation: '.$ticketFromDB->arrivalStation);

                $insuranceBodyInsured = InsuranceEnum::$insured;
                $insuranceBodyInsured['first_name'] = $ticketFromDB->firstName;
                $insuranceBodyInsured['last_name'] = $ticketFromDB->lastName;
                $insuranceBodyInsured['patronymic'] = $ticketFromDB->middleName;
                $insuranceBodyInsured['birth_date'] = date('Y-m-d', strtotime($ticketFromDB->birthday));
                $insuranceBodyInsured['gender'] = $ticketFromDB->gender == 'M' ? 'MALE' : 'FEMALE';
                $insuranceBodyInsured['phone']['number'] = $user->phone;
                $insuranceBodyInsured['ticket']['price']['value'] = $ticketFromDB->price;
                $insuranceBodyInsured['ticket']['issue_date'] = $ticketFromDB->created_at;
                $insuranceBodyInsured['ticket']['number'] = $ticketFromDB->ticketNum;

                $insuranceBody['insureds'][] = $insuranceBodyInsured;

                $alfaStrahResponse = Http::withHeaders([
                    'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
                ])->withBody(json_encode($insuranceBody), 'application/json')->post(env('ALFASTRAH_SERVICE_URL').'/policies?confirm=true');
                $alfaStrahResponse = json_decode($alfaStrahResponse);
                $policies = $alfaStrahResponse->policies;
                $policy = $policies[0];

                
                $policiesTotalRate += $policy->rate[0]->value;
                $ticketFromDB->insurance = json_encode($policy);
                $ticketFromDB->save();
            }
            $insuranceReceivePosition = FermaEnum::$insurance;
            $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policiesTotalRate;
            $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total + $order->duePrice + $policiesTotalRate;
        }
        

        $percent['Price'] = $percent['Amount'] = $order->duePrice;
        $body['Request']['CustomerReceipt']['Items'][] = $percent;        

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

        $order->ip = isset($orderFromBank->Ip) ? $orderFromBank->Ip : null;
        $order->pan = isset($orderFromBank->Pan) ? $orderFromBank->Pan : null;
        $order->save();

        Log::info('Order\'s confirmed'.$request->orderNumber.' '.$request->mdOrder);
    }

    public function getBack(Request $request){
        //найти заказ
        $orderFromDB = Order::find($request->orderId);
        $tickets = $orderFromDB->tickets->where('status', 'S');


        //перебрать невозвращённые билеты: для каждого вызвать возврат, обновить бд билета, засунуть в массив для экваринга, засунуть в массив для чека, обновить файл билета
        if($tickets->count() == 0){
            return response([
                'errorMessage' => 'Все билеты возвращены'
            ]);
        }

        $body = FermaEnum::$body;
        $body['Request']['Type'] = 'IncomeReturn';
        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = 0;
        $returnCount = 0;
        foreach($tickets as $ticket){
            //возврат на е-трафике
            $ticket_json = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->post(env('AVTO_SERVICE_URL').'/ticket/return/'.$ticket->id);
            $ticket_obj = json_decode($ticket_json);
            if(!isset($ticket_obj->hash)){
                continue;
            }
            $returnCount ++;

            //возврат в бд
            $ticketFromDB = Ticket::find($ticket->id);
            $ticketFromDB->update((array) $ticket_obj);
            $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket_obj->hash.'.pdf';
            $file_name = basename($url);
            file_put_contents('tickets/'.$ticket_obj->hash.'_r.pdf', file_get_contents($url));


            //возврат позиции на эквайринге
            $orderBundle = (array)json_decode($ticketFromDB->orderBundle);
            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDB->bankOrderId,
                'amount' => $ticketFromDB->repayment * 100,
                'positionId' => $orderBundle['positionId']
            ];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do',
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST => true, // Метод POST
                CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
            ));
            $repayment = curl_exec($curl); // Выполняем запрос
            $repayment = json_decode($repayment);
            if($repayment->errorCode != 0){
                continue;
            }
            $item = FermaEnum::$item;
            $item['Label'] = 'Бил'.(!empty($ticketFromDB->ticketNum) ? ' №' : '').$ticketFromDB->ticketNum.' '.$ticketFromDB->dispatchDate.' Мст№'.$ticketFromDB->seat.' '.$ticketFromDB->lastName.' '.mb_substr($ticketFromDB->firstName, 0, 1).'. '.mb_substr($ticketFromDB->middleName, 0, 1).'.';
            $item['Price'] = $item['Amount'] = $ticketFromDB->repayment;
            $body['Request']['CustomerReceipt']['Items'][] = $item;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] += $ticketFromDB->repayment;
        }
        if(!$returnCount){
            return response([
                'errorMessage' => 'Ни один билет не возвращён!'
            ], 422);
        }

        //проверка на страховку
        if($orderFromDB->insured){
            $policyTotalRate = 0;
            foreach($tickets as $ticket){
                $policy = json_decode($ticket->insurance);
                $policyTotalRate += $policy->rate[0]->value;

                $policy_id = $policy->policy_id;

                $alfastrahBody = [
                    'number' => $ticket->ticketNum,
                    'date' => date('Y-m-d')
                ];
                $alfaStrahResponse = Http::withHeaders([
                    'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
                ])->withBody(json_encode($alfastrahBody), 'application/json')->delete(env('ALFASTRAH_SERVICE_URL').'/policies/'.$policy_id.'/refund');
                Log::info('alfaStrahResponse '.$alfaStrahResponse);
            }

            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDB->bankOrderId,
                'amount' => $policyTotalRate * 100,
                'positionId' => $orderFromDB->tickets->count()+1
            ];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do',
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST => true, // Метод POST
                CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
            ));
            $repayment = curl_exec($curl); // Выполняем запрос            

            $insuranceReceivePosition = FermaEnum::$insurance;
            $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policyTotalRate;
            $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] += $policyTotalRate;            
        }


        //проверить рейс на отмену и если что добавить комиссию
        $race_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$ticketFromDB->raceUid);
        // Log::info('race_json: '.$race_json);
        $race = json_decode($race_json);
        if($race->race->status->name == 'Отменён' || $race->race->status->name == 'Закрыт'){
            $duePrice = 0;
            foreach($tickets as $ticket){
                $duePrice += $ticket->duePrice;
            }
            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDB->bankOrderId,
                'amount' => $duePrice * 100,
                'positionId' => $orderFromDB->tickets->count()+2
            ];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do',
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST => true, // Метод POST
                CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
            ));
            $repayment = curl_exec($curl); // Выполняем запрос
            $repayment = json_decode($repayment);
            $percent = FermaEnum::$percent;
            $percent['Price'] = $percent['Amount'] = $duePrice;
            $body['Request']['CustomerReceipt']['Items'][] = $percent;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum']  += $duePrice;
        }

        //обновить бд заказа
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/order/'.$request->orderId);
        $orderFromDB->order_info = $order_json;
        $orderFromDB->save();


        //распечатать чек
        $transaction = Transaction::create([
            'StatusCode' => 0,
            'type' => 'IncomeReturn',
            'order_id' => $request->orderId
        ]);
        $body['Request']['InvoiceId'] = $transaction->id;
        $user = $orderFromDB->user;
        if($user->email){
            $body['Request']['CustomerReceipt']['Email'] = $user->email;
        }
        $ReceiptId = FermaService::receipt($body);
        $ReceiptId = json_decode($ReceiptId);
        $ReceiptId = $ReceiptId->Data->ReceiptId;
        $receipt = FermaService::getStatus($ReceiptId);
        $receipt = json_decode($receipt);
        $transaction->StatusCode = $receipt->Data->StatusCode;
        $transaction->ReceiptId = $receipt->Data->ReceiptId;
        if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
            $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
        }
        $transaction->save();
        return response([
            'tickets' => $tickets->count()
        ]);
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