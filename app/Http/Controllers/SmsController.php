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
    public function sendReset(Request $request){
        $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
        $currentTime = date('Y-m-d\TH:i:s', $currentTime);

        $smsCount = Sms::where([
            ['phone', '=', $request->phone],
            ['created_at', '>', $currentTime],
        ])->get()->count();
        if($smsCount >= 2){
            return response([
                'error' => 'Можно высылать не более 2 смс за 2 часа!'
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if(!$user){
            return response([
                'error' => 'Пользователя с таким номером не существует!'
            ], 422);
        }
        $sms = Sms::create([
            'phone' => $request->phone,
            'code' => random_int(100000, 999999),
            'used' => false,
            'type' => 'reset'
        ]);
        $smsService = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$request->phone.'&sign=BIZNES&text=Ваш код на '.'росвокзалы.рф'.': '.$sms->code);
        return response([
            'sms' => $sms,
            'service' => json_decode($smsService)
        ]);
    }

    public function getReset(Request $request){
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

    public function sendRegister(Request $request){
        $user = $request->user;
        $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
        $currentTime = date('Y-m-d\TH:i:s', $currentTime);

        $smsCount = Sms::where([
            ['created_at', '>', $currentTime],
            ['phone', '=', $user['phone']],
        ])->get()->count();
        if($smsCount >= 2){
            return response([
                'errorMessage' => 'Можно высылать не более 2 смс за 2 часа!'
            ], 422);
        }

        
        $validator = Validator::make($user, [
            'phone' => 'required|size:17|unique:users',
            'password' => 'required|between:7,30|confirmed',
            'formConditionTop' => 'accepted'
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
            'used' => false,
            'type' => 'register'
        ]);
        $smsService = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$user['phone'].'&sign=BIZNES&text=Ваш код на '.'росвокзалы.рф'.': '.$request->url.': '.$sms->code);
        return response([
            'sms' => $sms
        ]);
    }

    public function getRegister(Request $request){
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'register'],
            ['used', '=', false],
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
}