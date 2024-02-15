<?php

namespace App\Http\Controllers;

use App\Models\BusStation;
use Illuminate\Http\Request;

class BusStationController extends Controller
{
    public function all(Request $request){
        $busStations = BusStation::all();
        return response([
            'busStations' => $busStations
        ]);
    }
    public function one(Request $request){
        $busStation = BusStation::where([['title', $request->title], ['hidden', false]])->first();
        return response([
            'station' => $busStation
        ]);
    }

    public function create(Request $request){
        $busStation = BusStation::create([
            'title' => $request->title,
            'dispatch_point_id' => $request->dispatch_point_id,
            'hidden' => $request->hidden,
        ]);
    }

    public function delete(Request $request){
        $busStation = BusStation::find($request->id);
        $busStation->delete();
    }

    public function edit(Request $request){
        $busStation = BusStation::find($request->id);
        $busStation->title = $request->title;
        $busStation->hidden = $request->hidden;
        $busStation->dispatch_point_id = $request->dispatch_point_id;
        $busStation->data = json_encode(['content' => $request->content ? $request->content : '']);
        $busStation->save();
    }
}