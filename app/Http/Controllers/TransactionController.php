<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\FermaService;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function all(Request $request){
        $order = Order::find($request->orderId);
        $transactions = $order->transactions;
        foreach($transactions as $transaction){
            if($transaction->StatusCode != 2){
                $receipt = FermaService::getStatus($transaction->ReceiptId);
                $receipt = json_decode($receipt);
        
                $transaction->StatusCode = $receipt->Data->StatusCode;
                if(isset($receipt->Data->Device->OfdReceiptUrl) && !empty($receipt->Data->Device->OfdReceiptUrl)){
                    $transaction->OfdReceiptUrl = $receipt->Data->Device->OfdReceiptUrl;
                }
                $transaction->save();
            }
        }
        return response([
            'transactions' => $transactions
        ]);  
    }
}