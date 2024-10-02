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