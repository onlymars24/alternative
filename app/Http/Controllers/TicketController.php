<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Enums\FermaEnum;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\FermaService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    public function all(){
        $tickets = Ticket::all();
        return response([
            'tickets' => $tickets
        ]);
    }
    public function getBack(Request $request){
        $ticket_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->post(env('AVTO_SERVICE_URL').'/ticket/return/'.$request->ticketId);
        $ticket = json_decode($ticket_json);
        if(!isset($ticket->hash)){
            return response([
                'error' => $ticket
            ], 422);
        }
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

        //begin createPayment
        // $orderBundle = (array)$ticketFromDB->orderBundle;

        // $race_json = Http::withHeaders([
        //     'Authorization' => env('AVTO_SERVICE_KEY'),
        // ])->post(env('AVTO_SERVICE_KEY').'/race/summary/'.$ticketFromDB->raceUid);
        // $race = json_decode($race_json);
        // date_default_timezone_set($race->depot->timezone);
        // Log::info(strtotime($ticketFromDB->dispatchDate).' - '.strtotime("now"));
        // $timeUntillDispatch = strtotime($ticketFromDB->dispatchDate) - strtotime("now");
        // if($race->race->RaceStatus->name == 'Отменён'){
            
        // }
        // elseif($timeUntillDispatch > 2 * 3600){

        // }
        // elseif($timeUntillDispatch < 2 * 3600 && $timeUntillDispatch > 0){
            
        // }
        // elseif($timeUntillDispatch < 0 && $timeUntillDispatch > 3 * 3600){

        // }
        // else{
        //     return response([
        //         'error' => null
        //     ], 422);
        // }

        $refundItems = ['items' => [json_decode($ticketFromDB->orderBundle)]];
        $data = [
            'userName' => config('services.payment.userName'),
            'password' => config('services.payment.password'),
            'orderId' => $orderFromDb->bankOrderId,
            'amount' => $ticketFromDB->price * 100,
            'refundItems' => json_encode($refundItems)
        ];
        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/refund.do', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        ));
        $repayment = curl_exec($curl); // Выполняем запрос
        
        $repayment = json_decode($repayment);
        
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

        $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = $ticketFromDB->price;
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
}