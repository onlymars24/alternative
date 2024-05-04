<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\Order;
use App\Models\Ticket;
use App\Enums\FermaEnum;
use App\Mail\ReturnMail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\FermaService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\DeletePassportService;

class TicketController extends Controller
{
    public function all(){
      
        if(!Auth::user()->admin){
            return response([
                'errorMessage' => 'Ошибка доступа!'
            ], 401);
        }
        $tickets = Ticket::orderByDesc('id')->get();
        return response([
            'tickets' => $tickets
        ]);
    }
    
    public function getBack(Request $request){
        //возврат на е-трафике
        $ticket_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->post(env('AVTO_SERVICE_URL').'/ticket/return/'.$request->ticketId);
        $ticket_json = DeletePassportService::ticket($ticket_json);
        $ticket = json_decode($ticket_json);
        if(!isset($ticket->hash)){
            return response([
                'error' => $ticket
            ], 422);
        }

        //обновление в БД
        $ticketFromDB = Ticket::find($ticket->id);
        $ticketFromDB->update((array)$ticket);
        $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket->hash.'.pdf';
        $file_name = basename($url);
        file_put_contents('tickets/'.$ticket->hash.'_r.pdf', file_get_contents($url));
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/order/'.$request->orderId);
        $order_json = DeletePassportService::order($order_json);
        $orderFromDb = Order::find($request->orderId);
        $orderFromDb->order_info = $order_json;
        $orderFromDb->save();
        $orderBundle = (array)json_decode($ticketFromDB->orderBundle);
        $orderBundle['itemPrice'] = ($ticketFromDB->repayment - $ticketFromDB->bonusesPrice) * 100;
        $refundItems = ['items' => [$orderBundle]];

        $race_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$ticketFromDB->raceUid);
        Log::info('race_json: '.$race_json);
        $race = json_decode($race_json);
        //возврат в экваринге
                //проверка на отмену рейса
                
        $data = [
            'userName' => config('services.payment.userName'),
            'password' => config('services.payment.password'),
            'orderId' => $orderFromDb->bankOrderId,
            'amount' => ($ticketFromDB->repayment - $ticketFromDB->bonusesPrice) * 100,
            'positionId' => $orderBundle['positionId']
        ];
        // if($race->race->status->name == 'Отменён' || $race->race->status->name == 'Закрыт'){
        //     $data['amount'] = $ticketFromDB->repayment * 100;
        // }


        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $repayment = curl_exec($curl); // Выполняем запрос
        $repayment = json_decode($repayment);
        
        //пробитие чека
        $transaction = Transaction::create([
            'StatusCode' => 0,
            'type' => 'IncomeReturn',
            'order_id' => $request->orderId
        ]);
        $body = FermaEnum::$body;
        $item = FermaEnum::$item;
        $percent = FermaEnum::$percent;
        $body['Request']['Type'] = 'IncomeReturn';
        $body['Request']['InvoiceId'] = $transaction->id;

        $body['Request']['CustomerReceipt']['Items'][] = (array)json_decode($ticketFromDB->customerItem);
        $body['Request']['CustomerReceipt']['Items'][0]['Price'] = $body['Request']['CustomerReceipt']['Items'][0]['Amount'] = $ticketFromDB->repayment - $ticketFromDB->bonusesPrice;
        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = ($ticketFromDB->repayment - $ticketFromDB->bonusesPrice);
        // if($race->race->status->name == 'Отменён' || $race->race->status->name == 'Закрыт'){
        //     $body['Request']['CustomerReceipt']['Items'][0]['Price'] = $body['Request']['CustomerReceipt']['Items'][0]['Amount'] = $ticketFromDB->repayment;
        //     $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $ticketFromDB->repayment;
        // }

        


        if($ticketFromDB->insurance){
            $policy = json_decode($ticketFromDB->insurance);

            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDb->bankOrderId,
                'amount' => $policy->rate[0]->value * 100,
                'positionId' => $orderFromDb->tickets->count()+1
            ];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do',
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST => true, // Метод POST
                CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
            ));
            $repayment = curl_exec($curl); // Выполняем запрос
            $policy_id = $policy->policy_id;

            $alfastrahBody = [
                'number' => $ticketFromDB->ticketNum,
                'date' => date('Y-m-d')
            ];
            $alfaStrahResponse = Http::withHeaders([
                'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
            ])->withBody(json_encode($alfastrahBody), 'application/json')->delete(env('ALFASTRAH_SERVICE_URL').'/policies/'.$policy_id.'/refund');
            Log::info('alfaStrahResponse '.$alfaStrahResponse);
            $alfaStrahResponse = json_decode($alfaStrahResponse);
            $insuranceReceivePosition = FermaEnum::$insurance;
            $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policy->rate[0]->value;
            $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] += $policy->rate[0]->value;
        }

        //проверка на отмену рейса
        if($race->race->status->name == 'Отменён' || $race->race->status->name == 'Закрыт'){
            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDb->bankOrderId,
                'amount' => $ticketFromDB->duePrice * 100,
                'positionId' => $orderFromDb->tickets->count()+2
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
            $percent['Price'] = $percent['Amount'] = $ticketFromDB->duePrice;
            $body['Request']['CustomerReceipt']['Items'][] = $percent;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum']  += $ticketFromDB->duePrice;
            $ticketFromDB->raceCanceled = true;
            $ticketFromDB->save();
            $user = $ticketFromDB->order->user;
            if($ticketFromDB->bonusesPrice > 0){
                Bonus::create([
                    'amount' => $ticketFromDB->bonusesPrice,
                    'transaction' => 'plus',
                    'user_id' => $user->id,
                    'order_id' => $ticketFromDB->order_id,
                    'user_phone' => $user->phone,
                    'descr' => 'Оформлен частичный возврат заказа с ID: '.$ticketFromDB->order_id
                ]);
                $user->bonuses = $user->bonuses + $ticketFromDB->bonusesPrice;
                $user->save();
            }

        }

        $user = $orderFromDb->user;
        if($user->email){
            $body['Request']['CustomerReceipt']['Email'] = $user->email;
        }
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
        if($orderFromDb->user->email){
            Mail::to($orderFromDb->user->email)->bcc(env('TICKETS_MAIL'))->send(new ReturnMail([$ticketFromDB]));
        }
        Log::info('ticket: '.json_encode($ticket));
        Log::info('repayment: '.json_encode($repayment));
        return response([
            'ticket' => $ticket,
            'repayment' => $repayment
        ]);
    }


    public function getBackForce(Request $request){
        //возврат на е-трафике
        $ticket_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->post(env('AVTO_SERVICE_URL').'/ticket/return/'.$request->ticketId);
        $ticket = json_decode($ticket_json);
        if(!isset($ticket->hash)){
            return response([
                'error' => $ticket
            ], 422);
        }

        //обновление в БД
        $ticketFromDB = Ticket::find($ticket->id);
        $ticketFromDB->update((array)$ticket);
        $url = env('AVTO_SERVICE_TICKET_URL').'/'.$ticket->hash.'.pdf';
        $file_name = basename($url);
        file_put_contents('tickets/'.$ticket->hash.'_r.pdf', file_get_contents($url));
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/order/'.$request->orderId);
        $orderFromDb = Order::find($request->orderId);
        $orderFromDb->order_info = $order_json;
        $orderFromDb->save();
        $orderBundle = (array)json_decode($ticketFromDB->orderBundle);
        $orderBundle['itemPrice'] = $ticketFromDB->repayment * 100;
        $refundItems = ['items' => [$orderBundle]];


        //возврат в экваринге
        $data = [
            'userName' => config('services.payment.userName'),
            'password' => config('services.payment.password'),
            'orderId' => $orderFromDb->bankOrderId,
            'amount' => $ticketFromDB->repayment * 100,
            'positionId' => $orderBundle['positionId']
        ];


        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $repayment = curl_exec($curl); // Выполняем запрос
        $repayment = json_decode($repayment);
        
        //пробитие чека
        $transaction = Transaction::create([
            'StatusCode' => 0,
            'type' => 'IncomeReturn',
            'order_id' => $request->orderId
        ]);
        $body = FermaEnum::$body;
        $item = FermaEnum::$item;
        $percent = FermaEnum::$percent;
        $body['Request']['Type'] = 'IncomeReturn';
        $body['Request']['InvoiceId'] = $transaction->id;

        $body['Request']['CustomerReceipt']['Items'][] = (array)json_decode($ticketFromDB->customerItem);
        $body['Request']['CustomerReceipt']['Items'][0]['Price'] = $body['Request']['CustomerReceipt']['Items'][0]['Amount'] = $ticketFromDB->repayment;

        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $ticketFromDB->repayment;


        if($ticketFromDB->insurance){
            $policy = json_decode($ticketFromDB->insurance);

            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDb->bankOrderId,
                'amount' => $policy->rate[0]->value * 100,
                'positionId' => $orderFromDb->tickets->count()+1
            ];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/processRawPositionRefund.do',
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST => true, // Метод POST
                CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
            ));
            $repayment = curl_exec($curl); // Выполняем запрос
            $policy_id = $policy->policy_id;

            $alfastrahBody = [
                'number' => $ticketFromDB->ticketNum,
                'date' => date('Y-m-d')
            ];
            $alfaStrahResponse = Http::withHeaders([
                'X-API-Key' => env('ALFASTRAH_SERVICE_KEY'),
            ])->withBody(json_encode($alfastrahBody), 'application/json')->delete(env('ALFASTRAH_SERVICE_URL').'/policies/'.$policy_id.'/refund');
            Log::info('alfaStrahResponse '.$alfaStrahResponse);
            $alfaStrahResponse = json_decode($alfaStrahResponse);
            $insuranceReceivePosition = FermaEnum::$insurance;
            $insuranceReceivePosition['Price'] = $insuranceReceivePosition['Amount'] = $policy->rate[0]->value;
            $body['Request']['CustomerReceipt']['Items'][] = $insuranceReceivePosition;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] += $policy->rate[0]->value;
        }

        //проверка на отмену рейса
        $race_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$ticketFromDB->raceUid);
        Log::info('race_json: '.$race_json);
        $race = json_decode($race_json);
        if($race->race->status->name == 'Отменён' || $race->race->status->name == 'Закрыт' || $ticket->price == $ticket->repayment){
            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderFromDb->bankOrderId,
                'amount' => $ticketFromDB->duePrice * 100,
                'positionId' => $orderFromDb->tickets->count()+2
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
            $percent['Price'] = $percent['Amount'] = $ticketFromDB->duePrice;
            $body['Request']['CustomerReceipt']['Items'][] = $percent;
            $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum']  += $ticketFromDB->duePrice;
            $ticketFromDB->raceCanceled = true;
            $ticketFromDB->save();
        }

        $user = $orderFromDb->user;
        if($user->email){
            $body['Request']['CustomerReceipt']['Email'] = $user->email;
        }
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

        return response([
            'ticket' => $ticket,
            'repayment' => $repayment
        ]);
    }

    public function orderTickets(Request $request){
        // return response([
        //     'tickets' => $request->orderId
        // ]);
        $tickets = Order::find($request->orderId)->tickets;
        

        return response([
            'tickets' => $tickets
        ]);
    }
}