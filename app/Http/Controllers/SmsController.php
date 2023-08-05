<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    // public function sendOrder(Request $request){
    //     $sms = Sms::create([
    //         'phone' => $request->phone,
    //         'code' => random_int(100000, 999999),
    //         'type' => 'order'
    //     ]);

    //     return response([
    //         'sms' => $sms
    //     ]);
    // }

    // public function getOrder(Request $request){
    //     $sms = Sms::where([
    //         ['phone', '=', $request->phone],
    //         ['code', '=', $request->code],
    //         ['type', '=', 'order']
    //     ])->first();

    //     return response([
    //         'sms' => $request
    //     ]);
    // }

    public function sendReset(Request $request){
        $user = User::where('phone', $request->phone)->first();
        if(!$user){
            return response([
                'error' => 'Пользователя с таким номером не существует!'
            ], 422);
        }
        $sms = Sms::create([
            'phone' => $request->phone,
            'code' => random_int(100000, 999999),
            'type' => 'reset'
        ]);
        $smsService = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$request->phone.'&sign=BIZNES&text=Ваш код: '.$sms->code);
        // $smsService = Http::withHeaders([
        //     'Authorization' => env('SMS_SERVICE_KEY'),
        // ])->post('https://email:api_key@gate.smsaero.ru/v2/balance');
        return response([
            'sms' => $sms,
            'service' => json_decode($smsService)
        ]);
    }

    public function getReset(Request $request){
        // $temp = env("AVTO_SERVICE_KEY");
        // return response([
        //     'sms' => $temp
        // ]);
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            // ['code', '=', $request->code],
            ['type', '=', 'reset']
        ])->latest()->first();

        if($sms->code != $request->code){
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }

        return response([
            'sms' => $sms
        ]);
    }

    public function sendRegister(Request $request)
    {
        $user = $request->user;
        $validator = Validator::make($user, [
            'phone' => 'required|size:17|unique:users',
            'password' => 'required|between:7,30|confirmed',
            'formConditionTop' => 'accepted',
            'formConditionBottom' => 'accepted',
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $sms = Sms::create([
            'phone' => $user['phone'],
            'code' => random_int(100000, 999999),
            'user' => json_encode($user),
            'type' => 'register'
        ]);
        $smsService = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$user['phone'].'&sign=BIZNES&text=Ваш код: '.$sms->code);
        return response([
            'sms' => $sms
        ]);
    }

    public function getRegister(Request $request)
    {
        // return response([
        //     'sms' => $request->phone
        // ]);
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            // ['code', '=', $request->code],
            ['type', '=', 'register']
        ])->latest()->first();
        if($sms->code != $request->code){
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }
        return response([
            'sms' => $sms
        ]);
    }
}