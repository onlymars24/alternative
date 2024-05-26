<?php

namespace App\Services;

use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SmsService
{    
    public static function removeMask($phone){
        $number = $phone;
        $number = preg_replace("/[^0-9]/", "", $number);
        $number = "+7" . substr($number, 1);
        return $number;
    }
}