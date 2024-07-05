<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\BusStation;
use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertBusStationsMain extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $xmlStr = File::get(public_path(env('XML_FILE_NAME')));
        // Log::info($xmlStr);
        // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));

        $busStationsMain = Setting::where('name', 'busStationsMain')->first();


        $dispatchPoints = DispatchPoint::all();
        // $dispatchPoint = DispatchPoint::find(73707);
        // dd(!$dispatchPoint->bus_stations->count());
        $busStationsSetting = [];
        foreach($dispatchPoints as $dispatchPoint){
          if(!$dispatchPoint->bus_stations->count()){
            $busStation = BusStation::create([
                'title' => $dispatchPoint->name,
                'name' => 'Автовокзал '.$dispatchPoint->name,
                'dispatch_point_id' => $dispatchPoint->id,
                'hidden' => false,
            ]);
      
            $newLoc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;
            $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
            for($i = 0; $i < count($xml->url); $i++){
                $xml->url[$i]->lastmod = date('Y-m-d');
            }
            $xmlExist = false;
            // for($i = 0; $i < count($xml->url); $i++){
            //     if($xml->url[$i]->loc == $newLoc){
            //         $xmlExist = true;
            //         break;
            //     }
            // }
            if(!$xmlExist){
              $newNode = $xml->addChild('url');
              $newNode->addChild('loc', $newLoc);
              $newNode->addChild('lastmod', date('Y-m-d'));
              $newNode->addChild('changefreq', 'weekly');
              $newNode->addChild('priority', '1.0');
              File::put(env('XML_FILE_NAME'), $xml->asXML());
            }
          }
          $busStationsSetting[$dispatchPoint['region']][] = $dispatchPoint->toArray();
        }
        FtpLoadingService::put();
      
      
        foreach($busStationsSetting as $key => $region){
          usort($region, function($a, $b) {
            return strcmp($a['name'], $b['name']);
          });
          $busStationsSetting[$key] = $region;
        }
        ksort($busStationsSetting);
      
        $busStationsMain = Setting::where('name', 'busStationsMain')->first();
        $busStationsMain->data = json_encode(json_decode(json_encode($busStationsSetting)));
        $busStationsMain->save();
    }
}
