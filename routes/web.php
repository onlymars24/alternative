<?php

use App\Models\Order;
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
    // date_default_timezone_set('Asia/Novosibirsk');
    // Log::info(strtotime($ticketFromDB->dispatchDate).' - '.strtotime("now"));
    $order = Order::find(2083704);
    dd($order->tickets->count());
});


Route::post('/kassa/callback', function (Request $request) {
    Log::info('Kassa is done '.$request);
});

Route::get('/order/confirm/', [OrderController::class, 'confirm'])->name('order.confirm');

// Route::get('/payment/callback/', [PaymentController::class, 'callback'])->name('payment.callback');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');