<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function register(Request $request){
        return response([
            'payment' => 'ok'
        ]);
    }

    public function callback(Request $request){
        Log::info('Callback Result '.$request->orderNumber.' '.$request->mdOrder);
    }
}