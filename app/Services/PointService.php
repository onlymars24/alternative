<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Enums\FermaEnum;
use App\Models\DispatchPoint;
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

    public static function addNewDispatchPoint($dispatchPoint){
        $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
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
            
            $newLoc = env('FRONTEND_URL').'/автобус/'.$dispatchPoint->name.'/'.$arrivalPoint->name;
            $newNode = $xml->addChild('url');
            $newNode->addChild('loc', $newLoc);
            $newNode->addChild('lastmod', date('Y-m-d'));
            $newNode->addChild('changefreq', 'daily');
            $newNode->addChild('priority', '1.0');     
            
            
            File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
            FtpLoadingService::put();
            
            // $arrivalPoint->kladr_id = KladrService::connectPointIntoKladr($arrivalPoint);      
        }
    }
}