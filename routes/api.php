<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DateController;
use App\Http\Controllers\Api\RaceController;
use App\Http\Controllers\Api\RacesController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResources([
    'dispatch_points' => DispatchPointsController::class,
    'arrival_points' => ArrivalPointsController::class,
    'races' => RacesController::class,
    'race' => RaceController::class,
    'date' => DateController::class,
    'countries' => CountriesController::class
]);