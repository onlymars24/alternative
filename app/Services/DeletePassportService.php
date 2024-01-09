<?php

namespace App\Services;


class DeletePassportService
{    
    public static function order($order_json){
        $order_obj = json_decode($order_json);
        foreach($order_obj->tickets as $ticket){
            unset($ticket->docSeries, $ticket->docNum);
        }
        return json_encode($order_obj);
    }

    public static function ticket($ticket_json){
        $ticket = json_decode($ticket_json);
        unset($ticket->docSeries, $ticket->docNum);
        return json_encode($ticket); 
    }
}