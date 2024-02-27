<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterReset\SendDataMail;
use App\Mail\RegisterReset\DataErrorMail;
use Illuminate\Support\Facades\Validator;
use App\Mail\RegisterReset\CodeStatusMail;
use App\Mail\RegisterReset\DataSuccessMail;

class SmsController extends Controller
{
    public function sendReset(Request $request){
        Mail::to(env('MAIL_FEEDBACK'))->send(new SendDataMail($request->phone, 'сброса пароля'));
        //2 часа замер
        $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
        $currentTime = date('Y-m-d\TH:i:s', $currentTime);

        $smsCount = Sms::where([
            ['phone', '=', $request->phone],
            ['created_at', '>', $currentTime],
        ])->get()->count();
        if($smsCount >= 2){
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $request->phone, 'сброса пароля', ['frequency' => ['Можно высылать не более 2 смс за 2 часа!']]));
            return response([
                'errors' => ['phone' => ['Можно высылать не более 2 смс за 2 часа!']]
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required|size:17'
        ]);
        if($validator->fails()){
            $tempErrors = $validator->errors();
            $tempErrors = json_encode($tempErrors);
            $tempErrors = (array)json_decode($tempErrors);
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $request->phone, 'сброса пароля', $tempErrors));
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }

        $user = User::where('phone', $request->phone)->first();
        if(!$user){
            $notExistingNumberMessage = 'Проверьте правильность ввода номера телефона:';
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $request->phone, 'сброса пароля', ['phone' => [$notExistingNumberMessage]]));
            return response([
                'errors' => ['phone' => [$notExistingNumberMessage]]
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
        Mail::to(env('MAIL_FEEDBACK'))->send(new DataSuccessMail($request->phone, 'сброса пароля'));
        return response([
            'sms' => $sms,
            'service' => $smsService,
            'balanceData' => $balanceData
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
            Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail('+'.$request->phone, 'Сменя пароля', false));
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
        Mail::to(env('MAIL_FEEDBACK'))->send(new SendDataMail($user['phone'], 'регистрации'));
        $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
        $currentTime = date('Y-m-d\TH:i:s', $currentTime);

        $smsCount = Sms::where([
            ['created_at', '>', $currentTime],
            ['phone', '=', $user['phone']],
        ])->get()->count();
        if($smsCount >= 2){
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $user['phone'], 'регистрации', ['Частота' => ['Можно высылать не более 2 смс за 2 часа!']]));
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
            $tempErrors = $validator->errors();
            $tempErrors = json_encode($tempErrors);
            $tempErrors = (array)json_decode($tempErrors);
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $user['phone'], 'регистрации', $tempErrors));
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }

        $code = random_int(100000, 999999);
        $smsService = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$user['phone'].'&sign=BIZNES&text=Код на '.'росвокзалы.рф'.': '.$code.'
Поддержка в ВК-группе: vk.com/rosvokzaly');
        $smsService = json_decode($smsService);
        $sms = Sms::create([
            'id' => $smsService->data->id,
            'phone' => $user['phone'],
            'code' => $code,
            'user' => json_encode($user),
            'used' => false,
            'type' => 'register'
        ]);

        $sms->cost = isset($smsService->data->cost) ? $smsService->data->cost : null;
        $sms->status = isset($smsService->data->status) ? $smsService->data->status : null;
        $balanceData = Http::withHeaders([
            'Authorization' => env('SMS_SERVICE_KEY'),
        ])->get('https://email:api_key@gate.smsaero.ru/v2/balance');
        $balanceData = json_decode($balanceData);
        $sms->balance = isset($balanceData->data->balance) ? $balanceData->data->balance : null;
        $sms->save();
        Mail::to(env('MAIL_FEEDBACK'))->send(new DataSuccessMail($user['phone'], 'регистрации'));
        return response([
            'sms' => $sms,
            'service' => $smsService,
            'balanceData' => $balanceData
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
            Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail($request->phone, 'Регистрация', false));
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

    public function getAll(Request $request){
        $smsAll = Sms::orderByDesc('id')->get();
        foreach($smsAll as $sms){
            if(($sms->status != 1 || $sms->status != 6) && $sms->id > 1000){
                $smsService = Http::withHeaders([
                    'Authorization' => env('SMS_SERVICE_KEY'),
                ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/status?id='.$sms->id);
                $smsService = json_decode($smsService);
                $sms->status = isset($smsService->data->status) ? $smsService->data->status : $sms->status;
                $sms->save();
            }
        }
        return response([
            'sms' => $smsAll
        ]);
    }
}