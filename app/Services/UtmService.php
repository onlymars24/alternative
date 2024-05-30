<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UtmService
{
    public static function update($orderId){
        $order = Order::find($orderId);
        $user = $order->user;
        if($order->utm_source || $order->utm_medium || $order->utm_campaign || $order->utm_content){
            $user->utm_source = $order->utm_source;
            $user->utm_medium = $order->utm_medium;
            $user->utm_campaign = $order->utm_campaign;
            $user->utm_content = $order->utm_content;
            $user->save();
        }
        elseif($user->utm_source || $user->utm_medium || $user->utm_campaign || $user->utm_content){
            $order->utm_source = $user->utm_source;
            $order->utm_medium = $user->utm_medium;
            $order->utm_campaign = $user->utm_campaign;
            $order->utm_content = $user->utm_content;
            $order->save();
        }
    }
}