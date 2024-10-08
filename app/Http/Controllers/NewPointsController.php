<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\BusStation;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Models\CacheArrivalPoint;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class NewPointsController extends Controller
{
    public function get(){
        // return response([
        //     'newPoints' => [json_decode(json_encode([
        //         'id' => 80153,
        //         'name' => 'Новосибирск',
        //         'region' => 'Новосибирская область',
        //         'details' => 'Детали',
        //         'address' => '',
        //         'latitude' => '',
        //         'longitude' => '',
        //         'okato' => 50417000001,
        //         'place' => 1
        //     ]))]
        // ]);
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
                        // DispatchPoint::create([
                        //     'id' => $point->id,
                        //     'name' => $point->name,
                        //     'region' => $point->region,
                        //     'details' => $point->details,
                        //     'address' => $point->address,
                        //     'latitude' => $point->latitude,
                        //     'longitude' => $point->longitude,
                        //     'okato' => $point->okato,
                        //     'place' => $point->place
                        // ]);
                        $points[] = $point;
                    }
                }   
            }
        }
        return response([
            'newPoints' => $points
        ]);
    }

    public function add(Request $request){
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        foreach($request->newPoints as $point){
            $point = (object)$point;
            if(DispatchPoint::where('name', $point->name)->first()){
                continue;
            }
            $dispatchPoint = DispatchPoint::create([
                'id' => $point->id,
                'name' => $point->name,
                'region' => $point->region,
                'details' => $point->details,
                'address' => $point->address,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'okato' => $point->okato ? $point->okato : '1',
                'place' => $point->place ? $point->place : 1
            ]);
            $arrival_points = CacheArrivalPoint::where('dispatch_point_id', $dispatchPoint->id)->first();
            if(!$arrival_points){
                $arrival_points_remoted = Http::withHeaders([
                    'Authorization' => env('AVTO_SERVICE_KEY'),
                ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$dispatchPoint->id)->object();
                $arrival_points = CacheArrivalPoint::create([
                    'dispatch_point_id' => $dispatchPoint->id,
                    'arrival_points' => json_encode($arrival_points_remoted)
                ]);
            }
            $arrival_points = json_decode($arrival_points->arrival_points);
            foreach($arrival_points as $arrivalPoint){
                $newLoc = env('FRONTEND_URL').'/автобус/'.$dispatchPoint->name.'/'.$arrivalPoint->name;
                $newNode = $xml->addChild('url');
                $newNode->addChild('loc', $newLoc);
                $newNode->addChild('lastmod', date('Y-m-d'));
                $newNode->addChild('changefreq', 'daily');
                $newNode->addChild('priority', '1.0');
            }  
            File::put(env('XML_FILE_NAME'), $xml->asXML());
            FtpLoadingService::put();
        }


        // $busStationsMain = Setting::where('name', 'busStationsMain')->first();


        // // $dispatchPoints = DispatchPoint::all();
        // // $dispatchPoint = DispatchPoint::find(73707);
        // // dd(!$dispatchPoint->bus_stations->count());
        // $busStationsSetting = [];
        // foreach($request->newPoints as $dispatchPoint){
        //   if(!$dispatchPoint->bus_stations->count()){
        //     $busStation = BusStation::create([
        //         'title' => $dispatchPoint->name,
        //         'name' => 'Автовокзал '.$dispatchPoint->name,
        //         'dispatch_point_id' => $dispatchPoint->id,
        //         'hidden' => false,
        //     ]);
      
        //     $newLoc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;
        //     $xml = simplexml_load_file(env('XML_FILE_NAME'));
        //     $xmlExist = false;
        //     for($i = 0; $i < count($xml->url); $i++){
        //         if($xml->url[$i]->loc == $newLoc){
        //             $xmlExist = true;
        //             break;
        //         }
        //     }
        //     if(!$xmlExist){
        //       $newNode = $xml->addChild('url');
        //       $newNode->addChild('loc', $newLoc);
        //       $newNode->addChild('lastmod', date('Y-m-d'));
        //       $newNode->addChild('changefreq', 'weekly');
        //       $newNode->addChild('priority', '1.0');
        //       File::put(env('XML_FILE_NAME'), $xml->asXML());
        //     }
        //   }
        //   $busStationsSetting[$dispatchPoint['region']][] = $dispatchPoint->toArray();
        // }
        // FtpLoadingService::put();
      
      
        // foreach($busStationsSetting as $key => $region){
        //   usort($region, function($a, $b) {
        //     return strcmp($a['name'], $b['name']);
        //   });
        //   $busStationsSetting[$key] = $region;
        // }
        // ksort($busStationsSetting);
        
        // $busStationsMain = Setting::where('name', 'busStationsMain')->first();
        // $busStationsMain->data = json_encode(json_decode(json_encode($busStationsSetting)));
        // $busStationsMain->save();
    }
}