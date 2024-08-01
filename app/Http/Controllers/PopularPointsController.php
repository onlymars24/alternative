<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CacheArrivalPoint;
use App\Models\DispatchPoint;

class PopularPointsController extends Controller
{
    public function edit(Request $request){
        $dispatchPoint = DispatchPoint::find($request->id);
        $dispatchPoint->popular_arrival_points = json_encode($request->popular_arrival_points);
        $dispatchPoint->save();
    }
}