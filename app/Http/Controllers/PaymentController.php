<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function register(Request $request){
        // $data = [
        //             'userName' => config('services.payment.userName'),
        //             'password' => config('services.payment.password'),
        //             'orderNumber' => $request->orderNumber,
        //             'amount' => $request->amount,
        //             'returnUrl' => $request->returnUrl
        //         ];
        // $data = json_encode($data);
        // Log::info('Message before json'.$data);
        return response([
            'payment' => 'ok'
        ]);
        // // $payment = Http::withBody($body, 'application/json')->post('https://alfa.rbsuat.com/payment/rest/register.do');
        // $curl = curl_init(); // Инициализируем запрос
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://alfa.rbsuat.com/payment/rest/register.do', // Полный адрес метода
        //     CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
        //     CURLOPT_POST => true, // Метод POST
        //     CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
        // ));
        // $payment = curl_exec($curl); // Выполняем запрос
         
        // // $response = json_decode($response, true); // Декодируем из JSON в массив
        // curl_close($curl); // Закрываем соединение
        // // return $response; // Возвращаем ответ
        // return response([
        //     'payment' => json_decode($payment)
        // ]);
    }

    public function callback(Request $request){
        Log::info('Callback Result '.$request->orderNumber.' '.$request->mdOrder);
    }
}