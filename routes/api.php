<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RaceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\OrderController;
// use App\Http\Controllers\Api\RaceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\DateController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PageMainController;
use App\Http\Controllers\RacesXmlController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AllPointsController;
use App\Http\Controllers\Api\RacesController;
use App\Http\Controllers\DebuggingController;
use App\Http\Controllers\BusStationController;
use App\Http\Controllers\PassengersController;
use App\Http\Controllers\Api\SendSmsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Api\CountriesController;
use App\Http\Controllers\PopularPointsController;
use App\Http\Controllers\Api\ArrivalPointsController;
use App\Http\Controllers\RacesExistingMailController;
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
    Route::get('/orders', [OrderController::class, 'all']);
    Route::get('/passengers', [PassengersController::class, 'all']);
    Route::post('/passenger/delete', [PassengersController::class, 'delete']);
    Route::post('/passenger/edit', [PassengersController::class, 'edit']);
    Route::post('/passenger/save', [PassengersController::class, 'save']);
    Route::post('/ticket/return', [TicketController::class, 'getBack']);

    Route::post('/order/return', [OrderController::class, 'getBack']);
    Route::get('/order/tickets', [TicketController::class, 'orderTickets']);
    Route::get('/tickets', [TicketController::class, 'all']);
    Route::post('/debugging', [DebuggingController::class, 'get']);
});
    // Route::post('/force/ticket/return', [TicketController::class, 'getBackForce']);


Route::post('/order/transactions', [TransactionController::class, 'all']);

Route::get('/race', [RaceController::class, 'get']);

Route::post('/send/race/existing', [RacesExistingMailController::class, 'send']);



Route::get('/order', [OrderController::class, 'one']);
Route::post('/settings/cluster/due', [SettingsController::class, 'setClusterDue']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/send/feedback', [FeedbackController::class, 'send']);
Route::get('/get/feedback', [FeedbackController::class, 'get']);

Route::get('/page/main', [PageMainController::class, 'get']);
Route::post('/page/main', [PageMainController::class, 'edit']);


Route::get('/bus/stations', [BusStationController::class, 'all']);
Route::get('/bus/station', [BusStationController::class, 'one']);
Route::post('/bus/station/create', [BusStationController::class, 'create']);
Route::post('/bus/station/edit', [BusStationController::class, 'edit']);
Route::post('/bus/station/delete', [BusStationController::class, 'delete']);


Route::post('/popular/points/edit', [PopularPointsController::class, 'edit']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sms/reset', [SmsController::class, 'sendReset']);
Route::get('/sms/reset', [SmsController::class, 'getReset']);
Route::post('/sms/register', [SmsController::class, 'sendRegister']);
Route::get('/sms/register', [SmsController::class, 'getRegister']);
Route::post('/reset', [AuthController::class, 'reset']);
Route::get('/sms/all', [SmsController::class, 'getAll']);



Route::get('/settings/cluster/due', [SettingsController::class, 'getClusterDue']);

Route::get('/points', [AllPointsController::class, 'all']);

Route::get('/matches', [MatchController::class, 'all']);

Route::post('/match/create', [MatchController::class, 'create']);

Route::post('/match/delete', [MatchController::class, 'delete']);

Route::post('/match/replacement', [MatchController::class, 'replacement']);
Route::post('/races/xml/create', [RacesXmlController::class, 'create']);


Route::get('/events', [EventController::class, 'all']);
Route::get('/station/events', [EventController::class, 'stationOnes']);
Route::get('/event', [EventController::class, 'one']);
Route::post('/event/create', [EventController::class, 'create']);
Route::post('/event/edit', [EventController::class, 'edit']);
Route::post('/event/delete', [EventController::class, 'delete']);
Route::post('/event/add/station', [EventController::class, 'addStation']);
Route::post('/event/delete/station', [EventController::class, 'deleteStation']);






Route::apiResources([
    'dispatch_points' => DispatchPointsController::class,
    'arrival_points' => ArrivalPointsController::class,
    'races' => RacesController::class,
    // 'race' => RaceController::class,
    'date' => DateController::class,
    'countries' => CountriesController::class,
    'sms' => SendSmsController::class
]);