<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    public function getBack(Request $request){
        $ticket_json = Http::withHeaders([
            'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
        ])->post('https://cluster.avtovokzal.ru/gdstest/rest/ticket/return/'.$request->ticketId);
        $ticket = json_decode($ticket_json);
        if(!isset($ticket->hash)){
            return response([
                'error' => $ticket
            ], 422);
        }
        $url = 'https://cluster.avtovokzal.ru/gdstest/mvc/download/'.$ticket->hash.'.pdf';
        $file_name = basename($url);
        file_put_contents('tickets/'.$file_name, file_get_contents($url));
        $order_json = Http::withHeaders([
            'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
        ])->get('https://cluster.avtovokzal.ru/gdstest/rest/order/'.$request->orderId);
        $orderFromDb = Order::find($request->orderId);
        $orderFromDb->order_info = $order_json;
        $orderFromDb->save();
        return response([
            'ticket' => $ticket
        ]);
    }
}
