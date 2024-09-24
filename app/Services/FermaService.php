<?php

namespace App\Services;

use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FermaService
{    
    private static function getToken(){
        $body = ['Login' => env('FERMA_SERVICE_USER_NAME'), 'Password' => env('FERMA_SERVICE_PASSWORD')];
        $body = json_encode($body);
        $response = Http::withBody($body, 'application/json')->post(env('FERMA_SERVICE_URL').'/Authorization/CreateAuthToken');
        Log::info('response: '.$response);
        $response = json_decode($response);
        return isset($response->Data->AuthToken) ? $response->Data->AuthToken : '';
    }

    public static function receipt($body){
        $token = self::getToken();
        $body = json_encode($body);
        $response = Http::withBody($body, 'application/json')->post(env('FERMA_SERVICE_URL').'/kkt/cloud/receipt?AuthToken='.$token);
        Log::info('response: '.$response);
        return $response; 
    }

    public static function getStatus($receiptId){
        $token = self::getToken();
        $body = ["Request"=> [
            "ReceiptId"=> $receiptId
            ]
        ];
        $body = json_encode($body);
        $response = Http::withBody($body, 'application/json')->post(env('FERMA_SERVICE_URL').'/kkt/cloud/status?AuthToken='.$token);
        return $response;
    }
}