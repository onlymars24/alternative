<?php

namespace App\Services;

use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Http;

class FermaService
{    
    private static function getToken(){
        $body = ['Login' => 'fermatest2', 'Password' => 'Go2999483Mb'];
        $body = json_encode($body);
        $response = Http::withBody($body, 'application/json')->post('https://ferma-test.ofd.ru/api/Authorization/CreateAuthToken');
        return json_decode($response)->Data->AuthToken;
    }

    public static function receipt($body){
        $token = self::getToken();
        $body = json_encode($body);
        $response = Http::withBody($body, 'application/json')->post('https://ferma-test.ofd.ru/api/kkt/cloud/receipt?AuthToken='.$token);
        return $response; 
    }

    public static function getStatus($receiptId){
        $token = self::getToken();
        $body = ["Request"=> [
            "ReceiptId"=> $receiptId
            ]
        ];
        $body = json_encode($body);
        $response = Http::withBody($body, 'application/json')->post('https://ferma-test.ofd.ru/api/kkt/cloud/status?AuthToken='.$token);
        return $response;
    }
}