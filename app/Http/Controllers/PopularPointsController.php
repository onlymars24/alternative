<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CacheArrivalPoint;

class PopularPointsController extends Controller
{
    public function edit(Request $request){
        $cache_arrival_points = CacheArrivalPoint::where('dispatch_point_id', $request->id)->first();
        $cache_arrival_points->popular_arrival_points = json_encode($request->popular_arrival_points);
        $cache_arrival_points->save();
    }
}