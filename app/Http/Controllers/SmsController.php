<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        return response([
            'sms' => $sms
        ]);
    }

    public function getReset(Request $request){
        // return response([
        //     'phone' => $request->phone
        // ]);
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'reset']
        ])->first();

        if(!$sms){
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }

        return response([
            'sms' => $sms
        ]);
    }
}
