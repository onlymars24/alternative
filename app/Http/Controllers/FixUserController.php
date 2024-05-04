<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\FixUserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

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

    public function smsSend(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required|size:17'
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
        $currentTime = date('Y-m-d\TH:i:s', $currentTime);

        $smsCount = Sms::where([
            ['phone', '=', $request->phone],
            ['created_at', '>', $currentTime],
        ])->get()->count();
        if($smsCount >= 2){
            return response([
                'errors' => ['phone' => ['Можно высылать не более 2 смс за 2 часа!']]
            ], 422);
        }

        $code = random_int(100000, 999999);
        $smsService = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$request->phone.'&sign=BIZNES&text=Код на '.'росвокзалы.рф'.': '.$code.'
Поддержка в ВК-группе: vk.com/rosvokzaly');
        $smsService = json_decode($smsService);
        if(!isset($smsService->data->id)){
            return response([
                'errors' => ['phone' => ['Прошло слишком мало времени после предыдущего смс!']]
            ], 422);
        }
        $sms = Sms::create([
            'id' => $smsService->data->id,
            'phone' => $request->phone,
            'code' => $code,
            'used' => false,
            'type' => 'reset'
        ]);
        
        $sms->cost = isset($smsService->data->cost) ? $smsService->data->cost : null;
        $sms->status = isset($smsService->data->status) ? $smsService->data->status : null;
        $balanceData = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/balance');
        $balanceData = json_decode($balanceData);
        $sms->balance = isset($balanceData->data->balance) ? $balanceData->data->balance : null;
        $sms->save();
    }

    public function smsGet(Request $request){
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['used', '=', false],
            ['type', '=', 'reset']
        ])->first();

        if(!$sms){
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }
        $sms->used = true;
        $sms->save();

        return response([
            'sms' => $sms
        ]);
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
        Auth::loginUsingId($user->id);
        $token = Auth::user()->createToken('authToken')->accessToken;
        return response([
            'token' => $token
        ]);
    }
}