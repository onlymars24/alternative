<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use App\Mail\CustomMail;
use App\Models\WhatsAppSms;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $smsAll = Sms::where([['status', '!=', 1], ['status', '!=', 2], ['status', '!=', 6]])->get();
        foreach($smsAll as $sms){
            $smsService = Http::withHeaders([
                'Authorization' => env('SMS_SERVICE_KEY'),
            ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/status?id='.$sms->id);
            $smsService = json_decode($smsService);
            $sms->status = isset($smsService->data) && isset($smsService->data->status) ? $smsService->data->status : $sms->status;
            $sms->save();
        }
        $smsLast = Sms::orderByDesc('id')->first();
        if($smsLast->balance < 300){
          Mail::to(env('MAIL_FEEDBACK'))->send(new CustomMail('ПОПОЛНИТЕ БАЛАНС SMSAERO!!!', 'ПОПОЛНИТЕ БАЛАНС SMSAERO!!!'));
        }

        $smsWhatsAppAll = WhatsAppSms::where([['status', '!=', 2], ['status', '!=', 3], ['status', '!=', 4], ['status', '!=', 10]])->get();
        foreach($smsWhatsAppAll as $sms){
            $whatsAppWapico = Http::
            get(env('WAPICO_URL').'/task_status.php?access_token='.env('WAPICO_KEY').'&task_id='.$sms->id);
            Log::info($whatsAppWapico);
            $whatsAppWapico = json_decode($whatsAppWapico);
            $sms->status = isset($whatsAppWapico->data) && isset($whatsAppWapico->data[0]) && isset($whatsAppWapico->data[0]->status) ? $whatsAppWapico->data[0]->status : $sms->status;
            $sms->save();
        }

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

        $user = User::where([['phone', $request->phone], ['confirmed', true]])->first();
        if(!$user){
            $notExistingNumberMessage = 'Проверьте правильность ввода номера телефона:';
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $request->phone, 'сброса пароля', ['phone' => [$notExistingNumberMessage]]));
            return response([
                'errors' => ['phone' => [$notExistingNumberMessage]]
            ], 422);
        }
        $code = random_int(100000, 999999);
        $message = 'Код на росвокзалы.рф: '.$code.'
Поддержка в ВК-группе: vk.com/rosvokzaly';
        $whatsAppSent = false;
        $phoneWithoutMask = SmsService::removeMask($request->phone);
        $checkWhatsApp = Http::
        post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
        $checkWhatsApp = json_decode($checkWhatsApp);
        
        if($request->whatsAppChosen && isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
            
            $whatsAppService = Http::
            post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
            .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
            $whatsAppService = json_decode($whatsAppService);
            if(!isset($whatsAppService->data->task_id)){
                Log::info('whatsAppService: '.json_encode($whatsAppService));
                return response([
                    'errors' => ['phone' => ['Произошла непредвиденная ошибка! Повторите позднее!']]
                ], 422);
            }

            $whatsAppSms = WhatsAppSms::create([
                'id' => $whatsAppService->data->task_id,
                'phone' => $user['phone'],
                'code' => $code,
                'used' => false,
                'type' => 'reset',
                'status' => 0
            ]);

            $whatsAppService2 = Http::
            get(env('WAPICO_URL').'/task_status.php?access_token='.env('WAPICO_KEY').'&task_id='.$whatsAppService->data->task_id);

            $whatsAppService2 = json_decode($whatsAppService2);
            $whatsAppSms->status = isset($whatsAppService2->data->status) ? $whatsAppService2->data->status : $whatsAppSms->status;
            $whatsAppSms->save();   
            $whatsAppSent = true;
        }
        else{
            $smsService = Http::withHeaders([
                'Authorization' => env('SMS_SERVICE_KEY'),
            ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$request->phone.'&sign=BIZNES&text='.$message);
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
            $sms->balance = isset($balanceData->data) && isset($balanceData->data->balance) ? $balanceData->data->balance : null;
            $sms->save();
        }



        Mail::to(env('MAIL_FEEDBACK'))->send(new DataSuccessMail($request->phone, 'сброса пароля'));
        return response([
            // 'sms' => $sms,
            // 'service' => $smsService,
            'whatsAppSent' => $whatsAppSent
        ]);
    }

    public function getReset(Request $request){
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'reset'],
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
            ['type', '=', 'reset'],
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
            Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail('+'.$request->phone, 'Сменя пароля', false));
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }
    }

    public function sendRegister(Request $request){ 
        $smsAll = Sms::where([['status', '!=', 1], ['status', '!=', 2], ['status', '!=', 6]])->get();
        foreach($smsAll as $sms){
            $smsService = Http::withHeaders([
                'Authorization' => env('SMS_SERVICE_KEY'),
            ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/status?id='.$sms->id);
            $smsService = json_decode($smsService);
            $sms->status = isset($smsService->data) && isset($smsService->data->status) ? $smsService->data->status : $sms->status;
            $sms->save();
        }
        $smsLast = Sms::orderByDesc('id')->first();
        if($smsLast->balance < 300){
          Mail::to(env('MAIL_FEEDBACK'))->send(new CustomMail('ПОПОЛНИТЕ БАЛАНС SMSAERO!!!', 'ПОПОЛНИТЕ БАЛАНС SMSAERO!!!'));
        }
        $user = $request->user;
        Mail::to(env('MAIL_FEEDBACK'))->send(new SendDataMail($user['phone'], 'регистрации'));

        $smsWhatsAppAll = WhatsAppSms::where([['status', '!=', 2], ['status', '!=', 3], ['status', '!=', 4], ['status', '!=', 10]])->get();
        foreach($smsWhatsAppAll as $sms){
            $whatsAppWapico = Http::
            get(env('WAPICO_URL').'/task_status.php?access_token='.env('WAPICO_KEY').'&task_id='.$sms->id);
            Log::info($whatsAppWapico);
            $whatsAppWapico = json_decode($whatsAppWapico);
            $sms->status = isset($whatsAppWapico->data) && isset($whatsAppWapico->data[0]) && isset($whatsAppWapico->data[0]->status) ? $whatsAppWapico->data[0]->status : $sms->status;
            $sms->save();
        }


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
            'phone' => 'required|size:17',
            'password' => 'required|between:7,30|confirmed',
            'formConditionTop' => 'accepted'
        ]);
        $userExisted = User::where([['phone', $user['phone']], ['confirmed', true]])->first();
        if($validator->fails() || $userExisted){
        // if($validator->fails()){
            $tempErrors = $validator->errors();
            $tempErrors = json_encode($tempErrors);
            $tempErrors = (array)json_decode($tempErrors);
            if($userExisted){
                $tempErrors['phone'][] = 'Данный номер телефона уже зарегистрирован.';
            }
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $user['phone'], 'регистрации', $tempErrors));
            return response(
                [
                    'errors' => (object)$tempErrors
                ], 422
            );
        }

        $code = random_int(100000, 999999);
        $message = 'Код на росвокзалы.рф: '.$code.'
Поддержка в ВК-группе: vk.com/rosvokzaly';
//         $messageWhatsApp = 'Код на росвокзалы.рф: '.$code.'
// Поддержка в ВК-группе: vk.com/rosvokzaly';
        $whatsAppSent = false;


        $phoneWithoutMask = SmsService::removeMask($user['phone']);
        $checkWhatsApp = Http::
        post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
        $checkWhatsApp = json_decode($checkWhatsApp);
        
        if($request->whatsAppChosen && isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
            
            $whatsAppService = Http::
            post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
            .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
            $whatsAppService = json_decode($whatsAppService);
            if(!isset($whatsAppService->data->task_id)){
                Log::info('whatsAppService: '.json_encode($whatsAppService));
                return response([
                    'errors' => ['phone' => ['Произошла непредвиденная ошибка! Повторите позднее!']]
                ], 422);
            }

            $whatsAppSms = WhatsAppSms::create([
                'id' => $whatsAppService->data->task_id,
                'phone' => $user['phone'],
                'code' => $code,
                'used' => false,
                'type' => 'register',
                'status' => 0
            ]);

            $whatsAppService2 = Http::
            get(env('WAPICO_URL').'/task_status.php?access_token='.env('WAPICO_KEY').'&task_id='.$whatsAppService->data->task_id);

            $whatsAppService2 = json_decode($whatsAppService2);
            $whatsAppSms->status = isset($whatsAppService2->data->status) ? $whatsAppService2->data->status : $whatsAppSms->status;
            $whatsAppSms->save();   
            $whatsAppSent = true;
        }
        else{
            $smsService = Http::withHeaders([
                'Authorization' => env('SMS_SERVICE_KEY'),
            ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$user['phone'].'&sign=BIZNES&text='.$message);
            $smsService = json_decode($smsService);
            if(!isset($smsService->data->id)){
                return response([
                    'errors' => ['phone' => ['Прошло слишком мало времени после предыдущего смс!']]
                ], 422);
            }
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
            $sms->balance = isset($balanceData->data) && isset($balanceData->data->balance) ? $balanceData->data->balance : null;
            $sms->save();  
            $whatsAppSent = false;          
        }

        Mail::to(env('MAIL_FEEDBACK'))->send(new DataSuccessMail($user['phone'], 'регистрации'));
        return response([
            // 'sms' => $sms,
            // 'service' => $smsService,
            // 'balanceData' => $balanceData,
            'whatsAppSent' => $whatsAppSent
        ]);
    }

    public function getRegister(Request $request){
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'register'],
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
            ['type', '=', 'register'],
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
            Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail($request->phone, 'Регистрация', false));
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }
    }

    public function getAll(Request $request){
        $smsAll = Sms::orderByDesc('id')->get();
        return response([
            'sms' => $smsAll
        ]);
    }

    public function getWhatsAppAll(Request $request){
        $smsWhatsAppAll = WhatsAppSms::orderByDesc('id')->get();
        return response([
            'smsWhatsAppAll' => $smsWhatsAppAll
        ]);
    }

    
}