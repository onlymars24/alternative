<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Passenger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function book(Request $request){
        // dd($request->sale);
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
        Order::create([
            'id' => $order->id,
            'order_info' => $order_json,
            'user_id' => Auth::id(),
        ]);
        foreach($order->tickets as $ticket){
            $ticketNew = (array)$ticket;
            $ticketNew['order_id'] = $order->id;
            Ticket::create(
                $ticketNew
            );
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
        return response([
            'order' => $order
        ]);
    }

    public function confirm(Request $request){
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->post('https://cluster.avtovokzal.ru/gdstest/rest/order/confirm/'.$request->order_id.'/По банковской карте');
        $order_obj = json_decode($order_json);
        
        foreach($order_obj->tickets as $ticket){
            $ticketFromDB = Ticket::find($ticket->id);
            $ticketFromDB->update((array)$ticket);
            $url = 'https://cluster.avtovokzal.ru/gdstest/mvc/download/'.$ticket->hash.'.pdf';
            $file_name = basename($url);
            file_put_contents('tickets/'.$file_name, file_get_contents($url));
        }
        $order = Order::find($request->order_id);
        $order->order_info = $order_json;
        $order->save();
        return response([
            'order' => $order_obj
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