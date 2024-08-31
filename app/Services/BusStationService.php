<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\BusStation;
use App\Models\DispatchPoint;
use Illuminate\Support\Facades\File;


class BusStationService
{    
    public static function createByKladr($kladrId){
        $busStationArray = [];
        $newLocType = '';
        $kladr = Kladr::find($kladrId);
        $busStationArray['url_settlement_name'] = str_replace([' ', '/'], ['_', '-'], $kladr->name);
        $busStationArray['name'] = $kladr->name.' Автовокзалы и автостанции';
        $busStationArray['description'] = $kladr->name.' Автовокзалы и автостанции: расписание, справочная, билеты на автобус';
        $busStationArray['kladr_id'] = $kladrId;
        $newLocType = 'расписание';
        $busStationArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
        $busStation = BusStation::create($busStationArray);

        $newLoc = env('FRONTEND_URL').'/'.$newLocType.'/'.$busStationArray['url_region_code'].'/'.$busStationArray['url_settlement_name'];
        $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        // for($i = 0; $i < count($xml->url); $i++){
        //     $xml->url[$i]->lastmod = date('Y-m-d');
        // }
        for($i = 0; $i < count($xml->url); $i++){
            if($xml->url[$i]->loc == $newLoc){
                // File::put(env('XML_FILE_NAME'), $xml->asXML());
                // FtpLoadingService::put();
                return [
                    'existing' => true
                ];
            }
        }
        $newNode = $xml->addChild('url');
        $newNode->addChild('loc', $newLoc);
        $newNode->addChild('lastmod', date('Y-m-d'));
        $newNode->addChild('changefreq', 'weekly');
        $newNode->addChild('priority', '1.0');
        
        File::put(env('XML_FILE_NAME'), $xml->asXML());
        FtpLoadingService::put();
        return [
            'existing' => false
        ];
    }

    public static function createByDispatchPoint($dispatchPointId){
        $busStationArray = [];
        $newLocType = '';
        $dispatchPoint = DispatchPoint::find($dispatchPointId);
        $kladr = $dispatchPoint->kladr;
        if(!$kladr){
            return [
                'error' => 'Нет связки'
            ];
        }
        $busStationArray['url_settlement_name'] = str_replace([' ', '/'], ['_', '-'], $dispatchPoint->name);
        $busStationArray['name'] = 'Автовокзал '.$dispatchPoint->name;
        $busStationArray['description'] = 'Автовокзал '.$dispatchPoint->name.': расписание, справочная, билеты на автобус';
        $busStationArray['dispatch_point_id'] = $dispatchPointId;
        $newLocType = 'автовокзал';

        $busStationArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
        $busStation = BusStation::create($busStationArray);

        $newLoc = env('FRONTEND_URL').'/'.$newLocType.'/'.$busStationArray['url_region_code'].'/'.$busStationArray['url_settlement_name'];
        $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        // for($i = 0; $i < count($xml->url); $i++){
        //     $xml->url[$i]->lastmod = date('Y-m-d');
        // }
        for($i = 0; $i < count($xml->url); $i++){
            if($xml->url[$i]->loc == $newLoc){
                return [
                    'existing' => true
                ];
            }
        }
        $newNode = $xml->addChild('url');
        $newNode->addChild('loc', $newLoc);
        $newNode->addChild('lastmod', date('Y-m-d'));
        $newNode->addChild('changefreq', 'weekly');
        $newNode->addChild('priority', '1.0');
        
        File::put(env('XML_FILE_NAME'), $xml->asXML());
        FtpLoadingService::put();
        return [
            'existing' => false
        ];
    }
}