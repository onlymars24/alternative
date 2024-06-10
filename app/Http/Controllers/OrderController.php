<?php

namespace App\Http\Controllers;

use DateTimeZone;
use App\Models\User;
use App\Models\Bonus;
use App\Models\Order;
use App\Models\Ticket;
use App\Mail\OrderMail;
use App\Models\Setting;
use App\Enums\FermaEnum;
use App\Mail\ReturnMail;
use App\Models\Passenger;
use Nette\Utils\DateTime;
use App\Models\Transaction;
use App\Models\WhatsAppSms;
use App\Enums\InsuranceEnum;
use App\Services\SmsService;
use App\Services\UtmService;
use Illuminate\Http\Request;
use App\Services\FermaService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\DeletePassportService;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function book(Request $request){
        
        $user = null;
        if(!$request->auth){
            $validator = Validator::make($request->all(), [
                'phone' => 'required|size:17'
            ]);
            if($validator->fails()){
                return response(
                    [
                        'errors' => $validator->errors()
                    ], 422
                );
            }
            $user = User::create([
                'phone' => $request->phone,
                'confirmed' => false,
                'password' => Hash::make($request->newPassword)
            ]);
            
        }
        else{
            $user = User::where([['id', '=',$request->userId], ['confirmed', '=', true]])->first();
        }

        $body = json_encode($request->sale);
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->withBody($body, 'application/json')->post(env('AVTO_SERVICE_URL').'/order/book/'.$request->uid);
        $order = json_decode($order_json);
        if(!isset($order->id)){
            if(!$request->auth){
                $user->delete();
            }
            return response([
                'error' => $order
            ], 422);
        }
        $order_json = DeletePassportService::order($order_json);
        $order = json_decode($order_json);
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
            'dispatchPointId' => $request->dispatch_point_id,
            'arrivalPointId' => $request->arrival_point_id,
            'bonusesPrice' => $request->bonuses,
            'user_id' => $user->id,
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
        $firstIteration = true;
        foreach($order->tickets as $key => $ticket){
            $ticketNew = (array)$ticket;
            if($firstIteration){
                $firstIteration = false;
                $ticketNew['bonusesPrice'] = floor($request->bonuses / count($order->tickets)) + ($request->bonuses % count($order->tickets));
            }
            else{
                $ticketNew['bonusesPrice'] = floor($request->bonuses / count($order->tickets));
            }
            // $ticketNew['price'] = $ticketNew['price'] - $ticketNew['bonuses'];

            $ticketNew['order_id'] = $order->id;
            $orderBundleEl = [ "positionId"=> $key+1,
                "name" => 'Бил'.(!empty($ticketNew['ticketNum']) ? ' №' : '').' '.$ticketNew['ticketNum'].' '.$ticketNew['dispatchDate'].' Мст№'.$ticketNew['seat'].' '.$ticketNew['lastName'].' '.mb_substr($ticketNew['firstName'], 0, 1).'. '.mb_substr($ticketNew['middleName'], 0, 1).'.',
                "quantity" => [ "value"=> 1, "measure" => 0 ],
                "itemCode" => "NM-".($key+1),
                "tax"=> ["taxType"=> 0, "taxSum"=> 0],
                "itemPrice"=> ($ticketNew['price'] - $ticketNew['bonusesPrice']) * 100
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
                    'user_id' => $user->id
                ]);
            }
        }
        //begin createPayment
        $data = [
            'userName' => config('services.payment.userName'),
            'password' => config('services.payment.password'),
            'orderNumber' => $order->id,
            'amount' => ($order->total - $request->bonuses + $duePrice) * 100,
            'orderBundle' => json_encode($orderBundle),
            'returnUrl' => env('FRONTEND_URL').'/account',
            'sessionTimeoutSecs' => 870,
            'dynamicCallbackUrl' => env('BACKEND_URL').'/order/confirm/'
        ];

        if($request->insured){
            $data['amount'] = ($order->total - $request->bonuses + $duePrice + $request->insurancePrice) * 100;
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

        $utm_data = $request->utm_data;
        Log::info('utm_data: '.json_encode($utm_data));
        // Log::info('utm_data->utm_source: '.$utm_data['utm_source']);


        $orderFromDB->utm_source = isset($utm_data['utm_source']) ? $utm_data['utm_source'] : null;
        $orderFromDB->utm_medium = isset($utm_data['utm_medium']) ? $utm_data['utm_medium'] : null;
        $orderFromDB->utm_campaign = isset($utm_data['utm_campaign']) ? $utm_data['utm_campaign'] : null;
        $orderFromDB->utm_content = isset($utm_data['utm_content']) ? $utm_data['utm_content'] : null;
        $orderFromDB->referrer_url = isset($utm_data['referrer_url']) ? $utm_data['referrer_url'] : null;
        
        $orderFromDB->bankOrderId = $payment->orderId;
        $orderFromDB->formUrl = $payment->formUrl;
        $orderFromDB->bonusesPrice = $request->bonuses;
        $orderFromDB->save();
        curl_close($curl); // Закрываем соединение

        if(isset($orderFromDB->tickets[0]->dispatchDate) && $orderFromDB->tickets[0]->dispatchDate){
            $originalTime = $orderFromDB->tickets[0]->dispatchDate;
            $fromTimeZone = $orderFromDB->timezone;
            $toTimeZone = 'Europe/Moscow';
            $date = new DateTime($originalTime, new DateTimeZone($fromTimeZone));
            $date->setTimeZone(new DateTimeZone($toTimeZone));
            $convertedTime =  $date->format('Y-m-d H:i:s');
        
            $orderFromDB->moscowDispatchTime = $convertedTime;
            $orderFromDB->save();            
        }
        
        $phoneWithoutMask = SmsService::removeMask($user->phone);
        $checkWhatsApp = Http::
        post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
        $checkWhatsApp = json_decode($checkWhatsApp);

        if(isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
            $message = 'Благодарим за оформление электронного билета *'.$orderFromDB->tickets[0]->raceName.' '.date("d.m.Y", strtotime($orderFromDB->tickets[0]->dispatchDate)).' в '.date("H:i", strtotime($orderFromDB->tickets[0]->dispatchDate)).'*. 

Заказ необходимо оплатить в течение 15 минут с момента бронирования, иначе бронирование отменится.

На всякий случай дублируем ссылку на оплату: '.$orderFromDB->formUrl;
            $whatsAppService = Http::
            post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
            .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
            $whatsAppService = json_decode($whatsAppService);
            Log::info('whatsAppService: '.json_encode($whatsAppService));
            if(isset($whatsAppService->data->task_id)){
                $whatsAppSms = WhatsAppSms::create([
                    'id' => $whatsAppService->data->task_id,
                    'phone' => $user->phone,
                    'type' => 'paymentReminder',
                    'status' => 0,
                    'message' => $message
                ]);            
            }
        }

        if($request->auth){
            UtmService::update($orderFromDB->id);
        }

        return response([
            'order' => $order,
            'payment' => $payment
        ]);
    }

    public function confirm(Request $request){
        Log::info('entry!');
        Log::info('orderNumber '.$request->orderNumber);
        Log::info('operation '.$request->operation);
        Log::info('status '.$request->status);
        if(empty($request->orderNumber) || $request->operation != 'deposited' || $request->status == 0){
            return;
        }
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->post(env('AVTO_SERVICE_URL').'/order/confirm/'.$request->orderNumber.'/По банковской карте');
        Log::info('order_id: '.$request->orderNumber.'; obj_json: '.$order_json);
        $order_obj = json_decode($order_json);
        if(!isset($order_obj->id)){
            return;
        }
        $order_json = DeletePassportService::order($order_json);
        $order_obj = json_decode($order_json);

        $transaction = Transaction::create([
            'StatusCode' => 0,
            'type' => 'Income',
            'order_id' => $request->orderNumber
        ]);
        $body = FermaEnum::$body;
        $item = FermaEnum::$item;
        $percent = FermaEnum::$percent;
        // $bonuses = FermaEnum::$bonuses;
        $body['Request']['Type'] = 'Income';
        $body['Request']['InvoiceId'] = $transaction->id;
        foreach($order_obj->tickets as $ticket){
            $ticketFromDB = Ticket::find($ticket->id);
            $ticketFromDB->update((array)$ticket);
            $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket->hash.'.pdf';
            $file_name = basename($url);
            file_put_contents('tickets/'.$file_name, file_get_contents($url));

            $item['Label'] = 'Бил'.(!empty($ticket->ticketNum) ? ' №' : '').$ticket->ticketNum.' '.$ticket->dispatchDate.' Мст№'.$ticket->seat.' '.$ticket->lastName.' '.mb_substr($ticket->firstName, 0, 1).'. '.mb_substr($ticket->middleName, 0, 1).'.';
            $item['Price'] = $item['Amount'] = $ticketFromDB->price - $ticketFromDB->bonusesPrice;
            if($ticketFromDB->bonusesPrice > 0){
                $item['AdditionalRequisite'] = 'Цена без скидки: '.$ticketFromDB->price.'.00';
            }
            $body['Request']['CustomerReceipt']['Items'][] = $item;
            $ticketFromDB->customerItem = json_encode($item);
            $ticketFromDB->confirmed_at = $order_obj->finished;
            $ticketFromDB->save();
        }

        $order = Order::find($request->orderNumber);
        $order->order_info = $order_json;

        
        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total - $order->bonusesPrice + $order->duePrice;
        $user = $order->user;
        if($user->email){
            $body['Request']['CustomerReceipt']['Email'] = $user->email;
        }


        //insurance check
        if($order->insured){
            $policiesTotalRate = 0;
            foreach($order_obj->tickets as $ticket){
                if($ticket->ticketType != 'Багажный'){
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
                    Log::info($alfaStrahResponse);
                    $alfaStrahResponse = json_decode($alfaStrahResponse);
                    $policies = $alfaStrahResponse->policies;
                    $policy = $policies[0];

                    
                    $policiesTotalRate += $policy->rate[0]->value;
                    $ticketFromDB->insurance = json_encode($policy);
                    $ticketFromDB->save();
                }
            }
            $insuranceReceivePosition = FermaEnum::$insurance;
            $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policiesTotalRate;
            $order->insurancePrice = $policiesTotalRate;
            $order->save();
            $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $order_obj->total - $order->bonusesPrice + $order->duePrice + $policiesTotalRate;
        }
        

        $percent['Price'] = $percent['Amount'] = $order->duePrice;
        // $bonuses['Price'] = $bonuses['Amount'] = 0;
        // $bonuses['Label'] = 'Скидка '.$order->bonusesPrice.' бонусов';
        // $body['Request']['CustomerReceipt']['Items'][] = $bonuses;
        $body['Request']['CustomerReceipt']['Items'][] = $percent;
        // if($ticketFromDB->bonusesPrice > 0){
        //     $body['CustomUserProperty']['Name'] = 'Скидка';
        //     $body['CustomUserProperty']['Value'] = $order->bonusesPrice;
        // }


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
        

        if($order->user->email){
            Mail::to($order->user->email)->bcc(env('TICKETS_MAIL'))->send(new OrderMail($order->tickets));
            Log::info('sent!');
        }
        else{
            Log::info('not sent!');
        }

        // $user = User::find($order->user_id);
        if($order->bonusesPrice > 0){
            $user->bonuses_balance = $user->bonuses_balance - $order->bonusesPrice;
            Bonus::create([
                'amount' => $order->bonusesPrice,
                'transaction' => 'minus',
                'user_id' => $user->id,
                'order_id' => $order->id,
                'user_phone' => $user->phone,
                'descr' => 'Оформлен заказ с ID: '.$order->id
            ]);
        }
        
        $user->save();

        $acqDuePercent = null;
        $setting = Setting::where('name', 'dues')->first();
        $dues = (array)json_decode($setting->data);
        Log::info(json_encode($dues));

        if($order->pan){
            $acqDuePercent = $dues['acqCardDue'];
        }
        else{
            $acqDuePercent = $dues['acqSbpDue'];
        }


        foreach($order->tickets as $ticket){
            $ticket->acqPercent = $acqDuePercent;
            $tempDuePrice = $ticket->price + $ticket->duePrice - $ticket->bonusesPrice;
            if($ticket->insurance){
                $tempInsurance = json_decode($ticket->insurance);
                if(isset($tempInsurance->rate[0]->value) && $tempInsurance->rate[0]->value){
                    $tempDuePrice += $tempInsurance->rate[0]->value;
                }
            }
            $resultAcqPrice = $tempDuePrice * $acqDuePercent / 100;
            if($resultAcqPrice < 5){
                $resultAcqPrice = 5;
            }
            $ticket->acqPrice = $resultAcqPrice;
            $ticket->save();
        }
        $phoneWithoutMask = SmsService::removeMask($user->phone);
        $checkWhatsApp = Http::
        post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
        $checkWhatsApp = json_decode($checkWhatsApp);
      
        if(isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
            $message = '💳 *Получите Кэшбэк!*

*Благодарим за оформление электронного билета!*

Рекомендуем сразу посмотреть билеты на обратный рейс (при его наличии) на сайте
Росвокзалы.рф

🎫🚍 Также для вас доступна возможность *компенсировать до 50% стоимости поездки.* Если хотите получить частичную компенсацию, напишите в ответ слово *"кэшбэк"*.💰

Мы вышлем, что нужно для этого сделать.';
            $whatsAppService = Http::
            post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
            .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
            $whatsAppService = json_decode($whatsAppService);
            Log::info('whatsAppService: '.json_encode($whatsAppService));
            if(isset($whatsAppService->data->task_id)){
                $whatsAppSms = WhatsAppSms::create([
                    'id' => $whatsAppService->data->task_id,
                    'phone' => $user->phone,
                    'type' => 'Подтверждение заказа',
                    'status' => 0,
                    'message' => $message
                ]);            
            }
        }

        

        
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
        $mailTickets = [];
        foreach($tickets as $ticket){
            //возврат на е-трафике
            $ticket_json = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->post(env('AVTO_SERVICE_URL').'/ticket/return/'.$ticket->id);
            
            $ticket_obj = json_decode($ticket_json);
            if(!isset($ticket_obj->hash)){
                continue;
            }
            $ticket_json = DeletePassportService::ticket($ticket_json);
            $ticket_obj = json_decode($ticket_json);
            $returnCount ++;

            //возврат в бд
            $ticketFromDB = Ticket::find($ticket->id);
            $ticketFromDB->update((array) $ticket_obj);
            if($ticketFromDB->returned && $ticketFromDB->timezone){
                $originalTime = $ticketFromDB->returned;
                $fromTimeZone = $ticketFromDB->timezone;
                $toTimeZone = 'Europe/Moscow';
                $date = new DateTime($originalTime, new DateTimeZone($fromTimeZone));
                $date->setTimeZone(new DateTimeZone($toTimeZone));
                $convertedTime =  $date->format('Y-m-d H:i:s');
            
                $ticketFromDB->returnedMoscow = $convertedTime;
                $ticketFromDB->save();            
            }
            $mailTickets[] = $ticketFromDB;
            $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket_obj->hash.'.pdf';
            $file_name = basename($url);
            file_put_contents('tickets/'.$ticket_obj->hash.'_r.pdf', file_get_contents($url));


            //возврат позиции на эквайринге
            $orderBundle = (array)json_decode($ticketFromDB->orderBundle);
            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDB->bankOrderId,
                'amount' => ($ticketFromDB->repayment - $ticketFromDB->bonusesPrice) * 100 ,
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
            $item['Price'] = $item['Amount'] = ($ticketFromDB->repayment - $ticketFromDB->bonusesPrice);
            $body['Request']['CustomerReceipt']['Items'][] = $item;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] += ($ticketFromDB->repayment - $ticketFromDB->bonusesPrice);
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
                if($ticket->ticketType != 'Багажный'){
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
            $bonusesPrice = 0;
            foreach($tickets as $ticket){
                $duePrice += $ticket->duePrice;
                $bonusesPrice += $ticket->bonusesPrice;
            }
            $user = $orderFromDB->user;

            if($bonusesPrice > 0){
                Bonus::create([
                    'amount' => $bonusesPrice,
                    'transaction' => 'plus',
                    'user_id' => $user->id,
                    'order_id' => $orderFromDB->id,
                    'user_phone' => $user->phone,
                    'descr' => 'Оформлен возврат заказа с ID: '.$orderFromDB->id
                ]);
                $user->bonuses = $user->bonuses + $bonusesPrice;
                $user->save();                
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
        $order_json = DeletePassportService::order($order_json);
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

        if($orderFromDB->user->email){
            Mail::to($orderFromDB->user->email)->bcc(env('TICKETS_MAIL'))->send(new ReturnMail($mailTickets));
        }
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
            'order' => $order,
            'tickets' => $order->tickets
        ]);
    }
}