<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Models\CacheArrivalPoint;
use App\Services\PointService;
use Illuminate\Database\Eloquent\Builder;

class ArrivalController extends Controller
{
    public function paginate(Request $request){
        $whereArr = [];                
        if($request->pointNameSearch){
            $whereArr[] = ['name', 'like', '%'.$request->pointNameSearch.'%'];
        }
        if($request->kladrId){
            $whereArr[] = ['kladr_id', '=', $request->kladrId];
        }
        if($request->dispatchPointId){
            $whereArr[] = ['dispatch_point_id', '=', $request->dispatchPointId];
        }
        if($request->noKladr){
            $whereArr[] = ['kladr_id', '=', null];
        }
        if($request->stationId){
            $whereArr[] = ['station_id', '=', $request->stationId];
        }

        if($request->noStation){
            $whereArr[] = ['station_id', '=', null];
        }
        return response([
            'points' => CacheArrivalPoint::with('dispatchPoint', 'kladr', 'station')->where($whereArr)->paginate(13)
        ]);
    }

    public function all(Request $request){
        if($request->pointType == 'e'){
            // $arrivalPoints1 = CacheArrivalPoint::doesntHave('kladr')->where([['dispatch_point_id', '=', $request->pointId]])->get();
            // $arrivalPoints2 = CacheArrivalPoint::where([['dispatch_point_id', '=', $request->pointId]])->whereHas('kladr', function(Builder $query){
            //     $query->has('arrivalPoints', '>', 1);
            // })
            // ->with('kladr.arrivalPoints')
            // ->get();
            // PointService::kAndE($request->pointId);
            // $kladrs = Kladr::has('arrivalPoints')->whereHas('arrivalPoints', function(Builder $query) use ($request){
            //         $query->where([['dispatch_point_id', '=', $request->pointId]]);
            //     })->get()->toArray();

            // $arrivalPoints = CacheArrivalPoint::where([['dispatch_point_id', '=', $request->pointId]])->get()->toArray();

            // $result = array_reduce($arrivalPoints, function ($carry, $item) {
            //     if (!in_array($item['name'], array_column($carry, 'name'))
            //         || !in_array($item['region'], array_column($carry, 'region'))
            //         || !in_array($item['details'], array_column($carry, 'district'))
            //     ) {
            //         $carry[] = $item;
            //     }
            //     return $carry;
            // }, $kladrs);
    
            // for($i = 0; $i < count($result); $i++){
            //     $result[$i]['keyId'] = $i+1;
            // }
            return response([
                // 'kladrs' => ,
                'arrivalPoints' => PointService::kAndE($request->pointId)
            ]);
        }
        elseif($request->pointType == 'k'){
            $dispatchPoints = Kladr::find($request->pointId)->dispatchPoints;
            $result = [];
            foreach($dispatchPoints as $dispatchPoint){
                $x = PointService::kAndE($dispatchPoint->id);
                $name_a = array_column($result, 'name');
                $region_a = array_column($result, 'region');



                // Удаляем элементы с повторяющимися name и region
                $x = array_filter($x, function($item) use ($name_a, $region_a) {
                    return !in_array($item['name'], $name_a) && !in_array($item['region'], $region_a);
                });

                // Объединяем два массива
                $result = array_merge($result, $x);                

                // $result = array_merge($result, PointService::kAndE($dispatchPoint->id));
            }
            return response([
                'arrivalPoints' => $result,
            ]);
        }
    }
}