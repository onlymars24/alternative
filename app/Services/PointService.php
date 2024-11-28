<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Models\Station;
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

    public static function dispatchData($search = ''){
        $dispatchData = [];
        $stations = Station::has('dispatchPoints')->where('name', 'like', '%'.$search.'%')->get();
        $kladrs = Kladr::has('dispatchPoints')->where('name', 'like', '%'.$search.'%')->get();
        foreach($stations as $station){
            if($station->kladr){
                $dispatchData[$station->name.'_'.$station->kladr->id] = $station;
            }
        }

        foreach($kladrs as $kladr){
            $dispatchData[$kladr->name.'_'.$kladr->id] = $kladr;
        }


        return self::leftSidePrioritySort((array)$dispatchData, $search);
        // return (array)$dispatchData;
    }

    public static function arrivalDataBySourceId($sourceId, $search = ''){
        $sourceId = explode('-', $sourceId);
        $stations = [];
        $arrivalData = [];
        if($sourceId[0] == 'kladrs'){
            $kladr = Kladr::find($sourceId[1]);
            $stations = $kladr->stations;
        }
        if($sourceId[0] == 'stations'){
            $stations = [Station::find($sourceId[1])];
        }

        foreach($stations as $station){
            if(!$station->dispatchPoints){
                continue;
            }
            foreach($station->dispatchPoints as $dispatchPoint){
                // $arrivalDataTemp = self::arrivalDataByPointId($dispatchPoint->id);
                // foreach($arrivalDataTemp as $arrivalItem){
                //     $arrivalData[$arrivaItem->name.'_'.]
                // }
                $arrivalPoints = CacheArrivalPoint::where([
                    ['name', 'like', '%'.$search.'%'], 
                    ['kladr_id', '=', null],
                    ['station_id', '=', null],
                    ['dispatch_point_id', '=', $dispatchPoint->id],
                ])->get();
                foreach($arrivalPoints as $point){
                    $arrivalData[$point->name.'_'] = $point;
                }

                $arrivalStations = Station::where([
                    ['name', 'like', '%'.$search.'%'],
                ])->whereHas('arrivalPoints', function(Builder $query) use ($dispatchPoint){
                    $query->where([['dispatch_point_id', '=', $dispatchPoint->id]]);
                })->get();
                foreach($arrivalStations as $station){
                    $key = $station->name.'_'.$station->kladr_id;
                    if(array_key_exists($key, $arrivalData) && isset($arrivalData[$key]->district)){
                        continue;
                    }
                    $arrivalData[$key] = $station;
                }

                $arrivalKladrs = Kladr::where([
                    ['name', 'like', '%'.$search.'%'],
                ])->whereHas('arrivalPoints', function(Builder $query) use ($dispatchPoint){
                    $query->where([['dispatch_point_id', '=', $dispatchPoint->id]]);
                })->get();
                foreach($arrivalKladrs as $kladr){
                    $arrivalData[$kladr->name.'_'.$kladr->id] = $kladr;
                }
            }
        }
        // return $arrivalData;
        return self::leftSidePrioritySort($arrivalData, $search);
    }

    public static function arrivalDataByPointId($dispatchPointId){

    }

    public static function leftSidePrioritySort($array, $str){
        $str_lower = mb_strtolower($str, 'UTF-8');

        // Функция для сортировки массива по ключам
        uksort($array, function($a, $b) use ($str_lower) {
            // Приводим ключи к нижнему регистру для регистронезависимого сравнения
            $a_lower = mb_strtolower($a, 'UTF-8');
            $b_lower = mb_strtolower($b, 'UTF-8');
        
            // Проверяем, начинается ли ключ с подстроки $str
            $startsWithA = mb_substr($a_lower, 0, mb_strlen($str_lower, 'UTF-8')) === $str_lower;
            $startsWithB = mb_substr($b_lower, 0, mb_strlen($str_lower, 'UTF-8')) === $str_lower;
        
            // Сортировка: сначала элементы, у которых ключ начинается с $str
            if ($startsWithA && $startsWithB) {
                return 0;
            }
            if ($startsWithA) {
                return -1;
            }
            if ($startsWithB) {
                return 1;
            }
        
            // Если оба ключа не начинаются с $str, оставляем их на своих местах
            return 0;
        });
        return $array;
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