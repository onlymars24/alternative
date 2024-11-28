<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use App\Models\Station;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Models\CacheArrivalPoint;
use App\Services\PointService;
use Illuminate\Database\Eloquent\Builder;

class StationController extends Controller
{
    public function all(Request $request){
        return response(['stations' => Station::with('kladrStationPage', 'kladr')->where('name', 'like', '%'.$request->stationFilter.'%')->orderByDesc('id')->get()]);
    }

    public function create(Request $request){
        $station = Station::create([
            'name' => $request->name,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'kladr_id' => $request->kladr_id
        ]);
        return response([
            'station' => $station
        ]);
    }

    public function edit(Request $request){
        $station = Station::find($request->id);
        $station->name = $request->name;
        $station->address = $request->address;
        $station->longitude = $request->longitude;
        $station->latitude = $request->latitude;
        $station->save();
        return response([
            'station' => $station
        ]);
    }

    public function delete(Request $request){
        $station = Station::find($request->id);
        $station->delete();
    }

    public function oneById(Request $request){
        return response([
            'station' => Station::with('kladr')->find($request->id)
        ]);
    }

    public function addToDispatchPoint(Request $request){
        $dispatchPoint = DispatchPoint::find($request->dispatchPointId);
        $dispatchPoint->station_id = $request->stationId;
        $dispatchPoint->save();
    }

    public function addToArrivalPoint(Request $request){
        $arrivalPoint = CacheArrivalPoint::find($request->arrivalPointId);
        $arrivalPoint->station_id = $request->stationId;
        $arrivalPoint->save();
    }
    // dispatchPointId
    public function races(Request $request){
        // $arrivalKladrs = Kladr::whereHas('arrivalPoints', function(Builder $query) use ($request){
        //     $query->where([['dispatch_point_id', '=', $request->dispatchPointId]]);
        // })->get();


        return response(['arrivalKladrs' => PointService::arrivalDataBySourceId($request->sourceId)]);
    }
}