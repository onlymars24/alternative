<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Enums\FermaEnum;
use App\Models\DispatchPoint;
use App\Services\MailService;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class PointService
{
    public static function dispatchKandE(){
        $dispatchPoints = DispatchPoint::with('kladr.arrivalPoints', 'station.kladrStationPage', 'kladr.kladrStationPage')->get()->toArray();
        $kladrs = Kladr::has('dispatchPoints')->with('kladrStationPage')->get()->toArray();
        $result = array_reduce($dispatchPoints, function ($carry, $item) {
            if (!in_array($item['name'], array_column($carry, 'name'))
                || !in_array($item['region'], array_column($carry, 'region'))
                || !in_array($item['details'], array_column($carry, 'district'))
            ) {
                $carry[] = $item;
            }
            return $carry;
        }, $kladrs);

        for($i = 0; $i < count($result); $i++){
            $result[$i]['keyId'] = $i+1;
        }
        return $result;
    }

    public static function kAndE($pointId){
        $kladrs = Kladr::has('arrivalPoints')->whereHas('arrivalPoints', function(Builder $query) use ($pointId){
            $query->where([['dispatch_point_id', '=', $pointId]]);
        })->get()->toArray();

        $arrivalPoints = CacheArrivalPoint::where([['dispatch_point_id', '=', $pointId]])->with('kladr.dispatchPoints')->get()->toArray();

        $result = array_reduce($arrivalPoints, function ($carry, $item) {
            if (!in_array($item['name'], array_column($carry, 'name'))
                || !in_array($item['region'], array_column($carry, 'region'))
                || !in_array($item['details'], array_column($carry, 'district'))
            ) {
                $carry[] = $item;
            }
            return $carry;
        }, $kladrs);

        for($i = 0; $i < count($result); $i++){
            $result[$i]['keyId'] = $i+1;
        }
        return $result;
    }

    public static function addNewArrivalPoints($dispatchPoint){
        $arrivalPointsRemoted = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$dispatchPoint->id)->object();
        foreach($arrivalPointsRemoted as $arrivalPointRemoted){
            $arrivalPoint = CacheArrivalPoint::create([
                'arrival_point_id' => $arrivalPointRemoted->id,
                'name' => $arrivalPointRemoted->name,
                'slug' => SlugService::create($arrivalPointRemoted->name),// название очищенное от небуквенных символов
                'region' => $arrivalPointRemoted->region,
                'details' => $arrivalPointRemoted->details,
                'address' => $arrivalPointRemoted->address,
                'latitude' => $arrivalPointRemoted->latitude,
                'longitude' => $arrivalPointRemoted->longitude,
                'okato' => $arrivalPointRemoted->okato,
                'place' => $arrivalPointRemoted->place ? $arrivalPointRemoted->place : 1,
                'dispatch_point_id' => $dispatchPoint->id,
            ]);
            $arrivalPoint->save();   
        }
    }

    public static function checkNewPoints(){
        // ini_set('max_execution_time', 600);
        $regions = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/regions/643')->object();
        $points = [];
        foreach($regions as $region){
            $pointsTemp = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->get(env('AVTO_SERVICE_URL').'/dispatch_points/'.$region->id)->object();
            if($pointsTemp){
                foreach($pointsTemp as $point){
                    if(!DispatchPoint::where('name', $point->name)->first()){
                        $points[] = $point;
                    }
                }
            }
        }
        return $points;
    }

    public static function addNewPoints($newPoints){
        foreach($newPoints as $point){
            $point = (object)$point;
            if(DispatchPoint::where('name', $point->name)->first()){
                continue;
            }
            $dispatchPoint = DispatchPoint::create([
                'id' => $point->id,
                'name' => $point->name,
                'slug' => SlugService::create($point->name),
                'region' => $point->region,
                'details' => $point->details,
                'address' => $point->address,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'okato' => $point->okato ? $point->okato : '1',
                'place' => $point->place ? $point->place : 1
            ]);
            self::addNewArrivalPoints($dispatchPoint);
        }
        MailService::sendDump('Новые точки от e-traffic', $newPoints);
    }
}