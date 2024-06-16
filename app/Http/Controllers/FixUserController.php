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

//     public function smsSend(Request $request){
//         $validator = Validator::make($request->all(), [
//             'phone' => 'required|size:17'
//         ]);
//         if($validator->fails()){
//             return response(
//                 [
//                     'errors' => $validator->errors()
//                 ], 422
//             );
//         }
//         $currentTime = mktime(date('H')-2, date('i'), date('s'), date('n'), date('d'), date('Y'));
//         $currentTime = date('Y-m-d\TH:i:s', $currentTime);

//         $smsCount = Sms::where([
//             ['phone', '=', $request->phone],
//             ['created_at', '>', $currentTime],
//         ])->get()->count();
//         if($smsCount >= 2){
//             return response([
//                 'errors' => ['phone' => ['Можно высылать не более 2 смс за 2 часа!']]
//             ], 422);
//         }

//         $code = random_int(100000, 999999);
//         $message = 'Код на росвокзалы.рф: '.$code.'
// Поддержка в ВК-группе: vk.com/rosvokzaly';
//         $whatsAppSent = false;
//         $phoneWithoutMask = SmsService::removeMask($request->phone);
//         $checkWhatsApp = Http::
//         post(env('WAPICO_URL').'/send.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&instance_id='.env('WAPICO_INSTANCE_ID'));
//         $checkWhatsApp = json_decode($checkWhatsApp);
        
//         if($request->whatsAppChosen && isset($checkWhatsApp->data) && $checkWhatsApp->data == 1){
            
//             $whatsAppService = Http::
//             post(env('WAPICO_URL').'/task_add.php?access_token='.env('WAPICO_KEY').'&number='.$phoneWithoutMask.'&type=check&message='.$message
//             .'&instance_id='.env('WAPICO_INSTANCE_ID').'&timeout=0');
//             $whatsAppService = json_decode($whatsAppService);
//             if(!isset($whatsAppService->data->task_id)){
//                 Log::info('whatsAppService: '.json_encode($whatsAppService));
//                 return response([
//                     'errors' => ['phone' => ['Произошла непредвиденная ошибка! Повторите позднее!']]
//                 ], 422);
//             }

//             $whatsAppSms = WhatsAppSms::create([
//                 'id' => $whatsAppService->data->task_id,
//                 'phone' => $request->phone,
//                 'code' => $code,
//                 'used' => false,
//                 'type' => 'reset',
//                 'status' => 0
//             ]);

//             $whatsAppService2 = Http::
//             get(env('WAPICO_URL').'/task_status.php?access_token='.env('WAPICO_KEY').'&task_id='.$whatsAppService->data->task_id);

//             $whatsAppService2 = json_decode($whatsAppService2);
//             $whatsAppSms->status = isset($whatsAppService2->data->status) ? $whatsAppService2->data->status : $whatsAppSms->status;
//             $whatsAppSms->save();   
//             $whatsAppSent = true;
//         }
//         else{
//             $smsService = Http::withHeaders([
//                 'Authorization' => env('SMS_SERVICE_KEY'),
//             ])->get('https://email:api_key@gate.smsaero.ru/v2/sms/send?number='.$request->phone.'&sign=BIZNES&text='.$message);
//             $smsService = json_decode($smsService);
//             if(!isset($smsService->data->id)){
//                 return response([
//                     'errors' => ['phone' => ['Прошло слишком мало времени после предыдущего смс!']]
//                 ], 422);
//             }
//             $sms = Sms::create([
//                 'id' => $smsService->data->id,
//                 'phone' => $request->phone,
//                 'code' => $code,
//                 'used' => false,
//                 'type' => 'reset'
//             ]);
            
//             $sms->cost = isset($smsService->data->cost) ? $smsService->data->cost : null;
//             $sms->status = isset($smsService->data->status) ? $smsService->data->status : null;
//             $balanceData = Http::withHeaders([
//                 'Authorization' => env('SMS_SERVICE_KEY'),
//             ])->get('https://email:api_key@gate.smsaero.ru/v2/balance');
//             $balanceData = json_decode($balanceData);
//             $sms->balance = isset($balanceData->data) && isset($balanceData->data->balance) ? $balanceData->data->balance : null;
//             $sms->save();
//         }



//         Mail::to(env('MAIL_FEEDBACK'))->send(new DataSuccessMail($request->phone, 'сброса пароля'));
//         return response([
//             // 'sms' => $sms,
//             // 'service' => $smsService,
//             'whatsAppSent' => $whatsAppSent
//         ]);
//     }

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
            Mail::to(env('MAIL_FEEDBACK'))->send(new CodeStatusMail('+'.$request->phone, 'Смена пароля', false));
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