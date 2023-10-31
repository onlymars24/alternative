<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Enums\FermaEnum;
use Nette\Utils\DateTime;
use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use App\Services\FermaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
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
    $tickets = Ticket::all()->except(['id']);
    dd($tickets->toArray());
});


Route::get('/kassa/callback', function (Request $request) {

});

Route::get('/export/excel/', [ExcelController::class, 'export'])->name('export.excel');


Route::get('/order/confirm/', [OrderController::class, 'confirm'])->name('order.confirm');

// Route::get('/payment/callback/', [PaymentController::class, 'callback'])->name('payment.callback');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
