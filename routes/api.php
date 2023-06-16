<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Api\DateController;
use App\Http\Controllers\Api\RaceController;
use App\Http\Controllers\Api\RacesController;
use App\Http\Controllers\PassengersController;
use App\Http\Controllers\Api\SendSmsController;
use App\Http\Controllers\Api\CountriesController;
use App\Http\Controllers\Api\ArrivalPointsController;
use App\Http\Controllers\Api\DispatchPointsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->group(function(){
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/edit/email', [AuthController::class, 'editEmail']);
    Route::post('/sms/order', [SmsController::class, 'sendOrder']);
    Route::get('/sms/order', [SmsController::class, 'getOrder']);
    Route::post('/order/book', [OrderController::class, 'book']);
    Route::post('/order/confirm', [OrderController::class, 'confirm']);
    Route::get('/orders', [OrderController::class, 'all']);
    Route::get('/passengers', [PassengersController::class, 'all']);
    Route::post('/passenger/delete', [PassengersController::class, 'delete']);
    Route::post('/passenger/edit', [PassengersController::class, 'edit']);
    Route::post('/passenger/save', [PassengersController::class, 'save']);
    Route::post('/ticket/return', [TicketController::class, 'getBack']);
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sms/reset', [SmsController::class, 'sendReset']);
Route::get('/sms/reset', [SmsController::class, 'getReset']);
Route::post('/reset', [AuthController::class, 'reset']);


Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/tickets', [TicketController::class, 'all']);
Route::get('/order', [OrderController::class, 'one']);


Route::apiResources([
    'dispatch_points' => DispatchPointsController::class,
    'arrival_points' => ArrivalPointsController::class,
    'races' => RacesController::class,
    'race' => RaceController::class,
    'date' => DateController::class,
    'countries' => CountriesController::class,
    'sms' => SendSmsController::class
]);