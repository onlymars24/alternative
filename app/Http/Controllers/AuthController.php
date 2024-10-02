<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use App\Mail\OrderMail;
use App\Mail\CustomMail;
use App\Models\WhatsAppSms;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Services\MailService;
use App\Services\FixUserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterReset\SendDataMail;
use App\Mail\RegisterReset\DataErrorMail;
use Illuminate\Support\Facades\Validator;
use App\Mail\RegisterReset\CodeStatusMail;
use App\Mail\RegisterReset\DataSuccessMail;

class AuthController extends Controller
{
    public function sendSmsAuth(Request $request){
        $user = $request->user;
        SmsService::fixStatus($user);
        

        $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
        $currentTime = date('Y-m-d\TH:i:s', $currentTime);
        $smsCount = Sms::where([
            ['created_at', '>', $currentTime],
            ['phone', '=', $user['phone']],
        ])->get()->count();
        if(!$request->whatsAppChosen && $smsCount >= 2){
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $user['phone'], 'регистрации', ['Частота' => ['Можно высылать не более 2 смс за 2 часа!']]));
            return response([
                'errorMessage' => 'Можно высылать не более 2 смс за 2 часа!'
            ], 422);
        }

        
        $validator = Validator::make($user, [
            'phone' => 'required|size:17',
        ]);
        if($validator->fails()){
            $tempErrors = $validator->errors();
            $tempErrors = json_encode($tempErrors);
            $tempErrors = (array)json_decode($tempErrors);
            Mail::to(env('MAIL_FEEDBACK'))->send(new DataErrorMail(
                $user['phone'], 'регистрации', $tempErrors));
            return response(
                [
                    'errors' => (object)$tempErrors
                ], 422
            );
        }

                $code = random_int(100000, 999999);
        //         $message = 'Код на росвокзалы.рф: '.$code.'
        // Поддержка в ВК-группе: vk.com/rosvokzaly';
        // $messageWhatsApp = 'Код на росвокзалы.рф: '.$code.'
        // Поддержка в ВК-группе: vk.com/rosvokzaly';
        $whatsAppSent = false;


        $phoneWithoutMask = SmsService::removeMask($user['phone']);
        $checkWhatsApp = Http::
        post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
        Log::info($checkWhatsApp);
        $checkWhatsApp = json_decode($checkWhatsApp);
        
        if($request->whatsAppChosen && isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
            $whatsAppService1 = Http::
            post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message=Ваш код для входа на Росвокзалы.рф:
            &instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
            $whatsAppService1 = json_decode($whatsAppService1);
            if(!isset($whatsAppService1->data->task_id)){
                MailService::sendError(env('WAPICO_URL').'/task_add.php', $whatsAppService1);
                Log::info('whatsAppService: '.json_encode($whatsAppService1));
                return response([
                    'errors' => ['phone' => ['Произошла непредвиденная ошибка! Повторите позднее!']]
                ], 422);
            }
            sleep(2);
            $whatsAppService2 = Http::
            post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$code.'
            &instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
            $whatsAppService2 = json_decode($whatsAppService2);
            if(!isset($whatsAppService2->data->task_id)){
                MailService::sendError(env('WAPICO_URL').'/task_add.php', $whatsAppService2);
                Log::info('whatsAppService: '.json_encode($whatsAppService2));
                return response([
                    'errors' => ['phone' => ['Произошла непредвиденная ошибка! Повторите позднее!']]
                ], 422);
            }

            $whatsAppSms = WhatsAppSms::create([
                'id' => $whatsAppService2->data->task_id,
                'phone' => $user['phone'],
                'code' => $code,
                'used' => false,
                'type' => 'auth',
                'status' => 0
            ]);

            $whatsAppService3 = Http::
            get(env('WAPICO_URL').'/task_status.php?access_token='.env('WAPICO_KEY').'&task_id='.$whatsAppService2->data->task_id);

            $whatsAppService3 = json_decode($whatsAppService3);
            $whatsAppSms->status = isset($whatsAppService3->data->status) ? $whatsAppService3->data->status : $whatsAppSms->status;
            $whatsAppSms->save();   
            $whatsAppSent = true;
        }
        else{
            if($request->whatsAppChosen){
                MailService::sendError(env('WAPICO_URL').'/send.php', $checkWhatsApp);
            }
            $message = 'Код на росвокзалы.рф: '.$code.'
Поддержка в ВК-группе: vk.com/rosvokzaly';
            $smsService = Http::withHeaders([
                'Authorization' => env('SMS_SERVICE_KEY'),
            ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$user['phone'].'&sign=BIZNES&text='.$message);
            Log::info($smsService);
            $smsService = json_decode($smsService);
            if(!isset($smsService->data->id)){
                MailService::sendError('https://email:api_key@gate.smsaero.ru/v2/sms/send', $smsService);
                return response([
                    'errors' => ['phone' => ['Прошло слишком мало времени после предыдущего смс!']]
                ], 422);
            }
            $sms = Sms::create([
                'id' => $smsService->data->id,
                'phone' => $user['phone'],
                'code' => $code,
                'used' => false,
                'type' => 'auth'
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
            'whatsAppSent' => $whatsAppSent
        ]);
    }

    public function confirmAuth(Request $request){
        $smsWhatsApp = WhatsAppSms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'auth'],
            ['used', '=', false],
        ])->first();
        $sms = Sms::where([
            ['phone', '=', '+'.$request->phone],
            ['code', '=', $request->code],
            ['type', '=', 'auth'],
            ['used', '=', false],
        ])->first();
        $smsConfirmation = null;

        if($smsWhatsApp){
            $smsConfirmation = $smsWhatsApp;
            $smsWhatsApp->used = true;
            $smsWhatsApp->save();
        }
        elseif($sms){
            $smsConfirmation = $sms;
            $sms->used = true;
            $sms->save();
        }
        else{
            // Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail('+'.$request->phone, 'Сменя пароля', false));
            return response([
                'error' => 'Код подтверждения неверный!'
            ], 422);
        }

        // if(!Auth::attempt(['phone' => $smsConfirmation->phone, 'confirmed' => 1])){
        $user = User::where([['phone', $smsConfirmation->phone], [ 'confirmed', 1]])->first();
        if(!$user){
            $user = User::create([
                'phone' => '+'.$request->phone,
            ]);   
        }
        Auth::loginUsingId($user->id);

        FixUserService::auth($smsConfirmation->phone);
        
        return response([
            'token' => Auth::user()->createToken('authToken')->accessToken
        ]);
    }

    public function user(){
        return response([
            'user' => Auth::user()
        ]);
    }

    public function editEmail(Request $request){
        $user = Auth::user();

        $user->email = $request->email;

        $user->save();

        $order = $user->orders->last();

        $order_info = json_decode($order->order_info);
        if($user->email && $order_info && $order_info->status == 'S'){
            Mail::to($user->email)->bcc(env('TICKETS_MAIL'))->send(new OrderMail($order->tickets));
        }

        
        return response([
            'success' => true
        ]);
    }

    public function users(){
        return response([
            'users' => User::all()
        ]);
    }
}