<?php

use App\Enums\FermaEnum;
use Nette\Utils\DateTime;
use Illuminate\Http\Request;
use App\Services\FermaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    $body = FermaEnum::$body;
    $item = FermaEnum::$item;
    $percent = FermaEnum::$percent;
    $body['Request']['Type'] = 'IncomeReturn';
    $body['Request']['InvoiceId'] = 11223344;
    $item['Label'] = 'Race to Moscow Alan Tomas';
    $item['Price'] = 123;
    $item['Amount'] = 123;
    $body['Request']['CustomerReceipt']['Items'][] = $item;
    
    $percent['Price'] = 10;
    $percent['Amount'] = 10;

    $body['Request']['CustomerReceipt']['Items'][] = $percent;
    $body['Request']['CustomerReceipt']['PaymentItems'][0]['Sum'] = 133;
    // dd($body);
    dd(FermaService::getStatus('d70b4591-4285-456e-9a48-e51f23806bb3'));
});


Route::post('/kassa/callback', function (Request $request) {
    // $data = [
    //     'userName' => config('services.payment.userName'),
    //     'password' => config('services.payment.password'),
    //     'orderId' => '00888e58-8199-7ee4-9de4-35540223c29b'
    // ];
    // $curl = curl_init(); // Инициализируем запрос
    // curl_setopt_array($curl, array(
    //     // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
    //     CURLOPT_URL => 'https://alfa.rbsuat.com/payment/rest/getOrderStatus.do', 
    //     CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
    //     CURLOPT_POST => true, // Метод POST
    //     CURLOPT_POSTFIELDS => http_build_query($data) // Данные в запросе
    // ));
    // $orderFromBank = curl_exec($curl); // Выполняем запрос
    // curl_close($curl); // Закрываем соединение
    // dd(gettype($orderFromBank));
    Log::info('Kassa is done '.$request);
});

Route::get('/order/confirm/', [OrderController::class, 'confirm'])->name('order.confirm');

// Route::get('/payment/callback/', [PaymentController::class, 'callback'])->name('payment.callback');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');