<?php

namespace App\Services;

use App\Models\Sms;
use App\Models\WhatsAppSms;

class MessageService
{
    public static function filter($request){
        $filterArr = $request->filterArr;
        $whereArr = [];                
        if($filterArr['phone']['set']){
            $whereArr[] = ['phone', '=', $filterArr['phone']['value']];
        }
        if($filterArr['status']['set']){
            $whereArr[] = ['status', '=', $filterArr['status']['value']];
        }
        if($filterArr['type']['set']){
            $whereArr[] = ['type', '=', $filterArr['type']['value']];
        }
        if($filterArr['cost']['set']){
            $whereArr[] = ['cost', '=', $filterArr['cost']['value']];
        }
        $whereArr[] = ['created_at', '>', $request->period[0]];
        $whereArr[] = ['created_at', '<', $request->period[1]];
        if($request->messagesType == 'whatsapp'){
            return WhatsAppSms::where($whereArr)->orderByDesc('id')->get();
        }
        if($request->messagesType == 'sms'){
            return Sms::where($whereArr)->orderByDesc('id')->get();
        }
    }
}