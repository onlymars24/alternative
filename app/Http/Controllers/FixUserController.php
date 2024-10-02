<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use App\Models\Order;
use App\Models\WhatsAppSms;
use App\Services\SmsService;
use App\Services\UtmService;
use Illuminate\Http\Request;
use App\Services\FixUserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\RegisterReset\CodeStatusMail;
use App\Mail\RegisterReset\DataSuccessMail;

class FixUserController extends Controller
{
    public function unfixedUser(Request $request){
        $order = Order::where('bankOrderId', $request->bankOrderId)->first();
        $user = $order->user;
        if($user->confirmed){
            return response([
                'error' => 'Пользователь уже подтверждён!'
            ], 422);
        }
        return response(['phone' => $user->phone]);
    }


    public function smsGet(Request $request){
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'auth'],
            ['used', '=', false],
        ])->first();
        if($sms){
            $sms->used = true;
            $sms->save();
            return response([
                'sms' => $sms
            ]);
        }

        $smsWhatsApp = WhatsAppSms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'auth'],
            ['used', '=', false],
        ])->first();
        if($smsWhatsApp){
            $smsWhatsApp->used = true;
            $smsWhatsApp->save();
            return response([
                'smsWhatsApp' => $smsWhatsApp
            ]);
        }

        if(!$sms && !$smsWhatsApp){
            // Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail('+'.$request->phone, 'Смена пароля', false));
            return response([
                'errorMessage' => 'Код подтверждения неверный!'
            ], 422);
        }
    }

    public function fix(Request $request){
        $order = Order::where('bankOrderId', $request->bankOrderId)->first();
        $userNew = $order->user;
        $userNew->phone = $request->phone;
        $userNew->save();

        $userOld = User::where([['phone', '=', $request->phone], ['confirmed', '=', true]])->first();

        $user = null;
        if($userOld && $userNew && $userOld->id == $userNew->id){
            $user = $userOld;
        }
        elseif($userOld){
            $userNew->delete();
            $order->user_id = $userOld->id;
            $order->save();
            $user = $userOld;
        }
        else{
            $userNew->confirmed = true;
            $userNew->save();
            $user = $userNew;
        }
        FixUserService::auth($request->phone);
        UtmService::update($order->id);
        Auth::loginUsingId($user->id);
        $token = Auth::user()->createToken('authToken')->accessToken;
        return response([
            'token' => $token
        ]);
    }
}