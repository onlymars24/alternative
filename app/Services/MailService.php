<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\FermaEnum;
use App\Mail\ErrorApiMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MailService
{
    public static function sendError($info, $body){
        Mail::to(env('ERROR_MAIL_MARSEL'))->bcc([env('ERROR_MAIL_YOUGILE'), env('ERROR_MAIL_PAVEL')])->send(new ErrorApiMail($info, $body));
    }
}