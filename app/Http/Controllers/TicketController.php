<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
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
        ])->post('https://cluster.avtovokzal.ru/gdstest/rest/ticket/return/'.$request->ticketId);
        $ticket = json_decode($ticket_json);
        if(!isset($ticket->hash)){
            return response([
                'error' => $ticket
            ], 422);
        }
        $ticketFromDB = Ticket::find($ticket->id);
        $ticketFromDB->update((array)$ticket);
        $url = 'https://cluster.avtovokzal.ru/gdstest/mvc/download/'.$ticket->hash.'.pdf';
        $file_name = basename($url);
        file_put_contents('tickets/'.$ticket->hash.'_r.pdf', file_get_contents($url));
        $order_json = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get('https://cluster.avtovokzal.ru/gdstest/rest/order/'.$request->orderId);
        $orderFromDb = Order::find($request->orderId);
        $orderFromDb->order_info = $order_json;
        $orderFromDb->save();
        return response([
            'ticket' => $ticket
        ]);
    }
}