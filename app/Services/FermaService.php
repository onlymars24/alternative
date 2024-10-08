<?php

namespace App\Services;

use App\Enums\FermaEnum;
use App\Models\Transaction;
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

    public static function create($orderId, $user, $body){
        $transaction = Transaction::create([
            'StatusCode' => 0,
            'type' => $body['Request']['Type'],
            'order_id' => $orderId
        ]);
        $body['Request']['InvoiceId'] = $transaction->id;
        if($user->email){
            $body['Request']['CustomerReceipt']['Email'] = $user->email;
        }
        $ReceiptId = self::receipt($body);
        $ReceiptId = json_decode($ReceiptId);
        if(isset($ReceiptId->Data->ReceiptId)){
            $ReceiptId = $ReceiptId->Data->ReceiptId;
            $receipt = self::getStatus($ReceiptId);
            $receipt = json_decode($receipt);            
            if(isset($receipt->Data->StatusCode) && isset($receipt->Data->ReceiptId)){
                $transaction->StatusCode = $receipt->Data->StatusCode;
                $transaction->ReceiptId = $receipt->Data->ReceiptId;
            }
            else{
                MailService::sendError(env('FERMA_SERVICE_URL').'/kkt/cloud/status', $receipt);
            }

            if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
                $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
            }
        }
        else{
            MailService::sendError(env('FERMA_SERVICE_URL').'/kkt/cloud/receipt', $ReceiptId);
        }
        $transaction->save();
    }
}