<?php

namespace App\Http\Controllers;

use App\Models\BusRoute;
use Illuminate\Http\Request;

class BusRouteController extends Controller
{
    public function all(){
        return response([
            'busRoutes' => BusRoute::all()
        ]);
    }

    public function one(Request $request){
        return response([
            'busRoute' => BusRoute::where([['dispatchPointName', $request->dispatchPointName], ['arrivalPointName', $request->arrivalPointName]])->first()
        ]);
        
    }

    public function edit(Request $request){
        $busRoute = BusRoute::find($request->busRouteId);
        $busRoute->content = $request->content;
        $busRoute->save();
        return response([
            'busRoute' => $busRoute
        ]);
    }

    public function create(Request $request){
        $busRoute = BusRoute::where([['dispatchPointName', $request->dispatchPointName], ['arrivalPointName', $request->arrivalPointName]])->first();
        if($busRoute){
            return response([
                'errorMessage' => 'Контент для данного маршрута уже существует!'
            ], 422);
        }
        $busRoute = BusRoute::create([
            'dispatchPointName' => $request->dispatchPointName,
            'arrivalPointName' => $request->arrivalPointName,
        ]);
        return response([
            'busRoute' => $busRoute
        ]);
    }

    public function delete(Request $request){
        $busRoute = BusRoute::find($request->busRouteId);
        $busRoute->delete();
    }
}