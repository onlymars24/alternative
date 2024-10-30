<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public static function adPdf(){
        $setting = Setting::where('name', 'adPdf')->first();
        $data = json_decode($setting->data);
        return $data ? $data->adPdf : null;
    }
}