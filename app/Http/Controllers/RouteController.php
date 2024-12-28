<?php

namespace App\Http\Controllers;

use App\Models\KladrsCouple;
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

    public function getByKladrsCouple(Request $request){
        $kladrsCouple = KladrsCouple::where([
            ['dispatch_kladr_id', '=', $request->dispatchKladrId],
            ['arrival_kladr_id', '=', $request->arrivalKladrId],
        ])->first();

        return response(['routes' => $kladrsCouple ? $kladrsCouple->routes : []]);
    }
}
