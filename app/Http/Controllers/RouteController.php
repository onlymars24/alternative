<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function getByKladr(Request $request){
        return response(['routes' => Route::where('kladr_id', $request->kladrId)->get()]);
    }

    public function getByStation(Request $request){
        return response(['routes' => Route::where('station_id', $request->stationId)->get()]);
    }
}
