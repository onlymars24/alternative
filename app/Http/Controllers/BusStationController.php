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
        $busStation = BusStation::where('title', $request->title)->first();
        return response([
            'content' => isset($busStation->data) ? json_decode($busStation->data)->content : null
        ]);
    }

    public function create(Request $request){
        $busStation = BusStation::create([
            'title' => $request->title,
        ]);
    }

    public function delete(Request $request){
        $busStation = BusStation::find($request->id);
        $busStation->delete();
    }

    public function edit(Request $request){
        $busStation = BusStation::find($request->id);
        $busStation->title = $request->title;
        $busStation->data = json_encode(['content' => $request->content]);
        $busStation->save();
    }
}