<?php

namespace App\Services;

use App\Models\Sms;
use App\Enums\FermaEnum;
use App\Mail\CustomMail;
use App\Models\WhatsAppSms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\RegisterReset\SendDataMail;

class SmsService
{    
    public static function removeMask($phone){
        $number = $phone;
        $number = preg_replace("/[^0-9]/", "", $number);
        $number = "+7" . substr($number, 1);
        return $number;
    }

    public static function fixStatus($user){
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
        // $user = $request->user;
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
    }
}