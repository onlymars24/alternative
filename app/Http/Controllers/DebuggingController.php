<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DebuggingController extends Controller
{
    public function get(Request $request){
        // dd('');
        // return $request->orderId;
        // $orders = Order::where([
        //     ['created_at', '>', $request->comparingDate1],
        //     ['created_at', '<', $request->comparingDate2]
        // ])->get();
        $orderDB = Order::find($request->orderId);
        if(!$orderDB){
            return response([
                'bugs' => []
            ]);
        }
        // return response([
        //     'id' => $request->orderId,
        //     'orderDB' => $orderDB
        // ]);
        $bugs = [];
        // foreach($orders as $orderDB){
            $order_info = json_decode($orderDB->order_info);
            // return response([
            //     'order_info' => $order_info
            // ]);
            $orderRemoted = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->get(env('AVTO_SERVICE_URL').'/order/'.$orderDB->id);
            $orderRemoted = json_decode($orderRemoted);
            // return response([
            //     'orderRemoted' => $orderRemoted
            // ]);
            if(($order_info->status == 'B' && !isset($orderRemoted->status)) || $order_info->status != $orderRemoted->status){
                $bugs[$orderDB->id][] = 'Status заказа в е-траффик не совпадает со статусом в БД!';
            }
            foreach($orderRemoted->tickets as $ticket){
                $ticketDB = Ticket::find($ticket->id);
                if($ticketDB->status != $ticket->status){
                    $bugs[$orderDB->id][] = 'Status билета ID'.$ticket->id.' в е-трафик не совпадает со Status в БД tickets!';
                }
                if($ticketDB->price != $ticket->price){
                    $bugs[$orderDB->id][] = 'Price билета ID'.$ticket->id.' в е-трафик не совпадает с Price в БД tickets!';
                }
                if($ticketDB->repayment != $ticket->repayment){
                    $bugs[$orderDB->id][] = 'Repayment билета ID'.$ticket->id.' в е-трафик не совпадает с Repayment в БД tickets!';
                }
            }
            foreach($order_info->tickets as $ticket){
                $ticketDB = Ticket::find($ticket->id);
                if($ticketDB->status != $ticket->status){
                    $bugs[$orderDB->id][] = 'Status билета ID'.$ticket->id.' в БД orders не совпадает со Status в БД tickets!';
                }
                if($ticketDB->price != $ticket->price){
                    $bugs[$orderDB->id][] = 'Price билета ID'.$ticket->id.' в БД orders не совпадает с Price в БД tickets!';
                }
                if($ticketDB->repayment != $ticket->repayment){
                    $bugs[$orderDB->id][] = 'Repayment билета ID'.$ticket->id.' в БД orders не совпадает с Repayment в БД tickets!';
                }
            }
            
            foreach($orderDB->transactions as $transaction){
                if($transaction->StatusCode == 3 || empty($transaction->ReceiptId)){
                    $bugs[$orderDB->id][] = 'Ошибка транзакции с ID'.$transaction->id;
                }
            }
            $data = [
                'userName' => config('services.payment.userName'),
                'password' => config('services.payment.password'),
                'orderId' => $orderDB->bankOrderId
            ];
            $curl = curl_init(); // Инициализируем запрос
            curl_setopt_array($curl, array(
                // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
                CURLOPT_URL => env('PAYMENT_SERVICE_URL').'/getOrderStatus.do', 
                CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
                CURLOPT_POST => true, // Метод POST
                CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
            ));
            $payment = curl_exec($curl); // Выполняем запрос
            curl_close($curl); // Закрываем соединение
            $payment = json_decode($payment);
            // return response([
            //     'order_info' => $order_info,
            //     'payment' => $payment
            // ]);
//CHECK STATUSES AGAIN!!!!!!!!!!!
            if(
                ($order_info->status == 'S' && $payment->OrderStatus != 2)
                || ($order_info->status == 'P' && $payment->OrderStatus != 4)
                || ($order_info->status == 'R' && $payment->OrderStatus != 4)
                || ($order_info->status == 'B' && ($payment->OrderStatus != 0 || $payment->OrderStatus != 3 || $payment->OrderStatus != 6))
            ){
                $bugs[$orderDB->id][] = 'Статус заказа билетов не сходится со статусом заказа платежа!';
            }
            // return response([
            //     'order_info' => $order_info,
            //     'payment' => $payment
            // ]);
            if($order_info->total + $orderDB->duePrice != $payment->Amount / 100){
                $bugs[$orderDB->id][] = 'Стоимость заказа отличается от платежа!';
            }
            return response([
                'bugs' => $bugs
            ]);
        // }
    }
}