<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;


class FixUserService
{    
    //для запуска хотя бы один должен быть подтверждённым
    public static function auth($phone){
        $userConfirmed = User::where([['phone', $phone], ['confirmed', true]])->first();
        if(!$userConfirmed){
            return;
        }

        $users = User::where('phone', $phone)->get();
        if($users->count() < 2){
            return;
        }

        foreach($users as $user){
            if(!$user->confirmed && $user->orders){
                foreach($user->orders as $order){
                    $orderDB = Order::find($order->id);
                    $orderDB->user_id = $userConfirmed->id;
                    $orderDB->save();
                }
                $userDB = User::find($user->id);
                $userDB->delete();
            }
        }
    }
}