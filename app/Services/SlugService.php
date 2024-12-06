<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Models\Station;
use App\Enums\FermaEnum;
use App\Models\DispatchPoint;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SlugService
{
    public static function create($string){
        return str_replace([' ', '/', '\'', '"', '.', ',', '<', '>', '.', ','], ['_', '-', '-', '', '', '', '', '',  '', ''], $string);
    }


    public static function unifyStationSlug($station){
        $existingStation = Station::where([['slug', '=', $station->slug], ['id', '<', $station->id]])->first();
        if(!$existingStation){
            return;
        }
        Log::info('Кандидат '.json_encode($station));
        if(!isset($station->kladr)){
            Log::info('Нет кладр '.json_encode($station));
            // PROBLEM
            return;
        }
        if(!$station->kladr->district){
            Log::info('Нет района '.json_encode($station->kladr));
            // PROBLEM
            return;
        }
        $newSlug = $station->slug.'-'.SlugService::create($station->kladr->district);
        $existingStation = Station::where([['slug', '=', $newSlug], ['id', '<', $station->id]])->first();
        if(!$existingStation){
            Log::info('Новый slug '.$newSlug.' для : '.json_encode($station));
            $station->slug = $newSlug;
            $station->save();
            return;
        }
        if(!$station->kladr->region){
            Log::info('Нет региона '.json_encode($station->kladr));
            // PROBLEM
            return;
        }
        $newSlug = $newSlug.'-'.SlugService::create($station->kladr->region);
        $station->slug = $newSlug;
        $station->save();
        Log::info('Новый slug'.$newSlug.' для : '.json_encode($station));
    }

    public static function unifyKladrsSlugs(){
        $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();
        foreach($kladrs as $kladr){
            $existingKladr = $kladrs->where('slug', '=', $kladr->slug)->where('id', '<', $kladr->id)->first();
            if(!$existingKladr){
                continue;
            }
            Log::info('Кандидат '.json_encode($kladr));
            if(!$kladr->district){
                Log::info('Нет района '.json_encode($kladr));
                // PROBLEM
                continue;
            }
            $newSlug = $kladr->slug.'-'.SlugService::create($kladr->district);
            $existingKladr = $kladrs->where('slug', '=', $newSlug)->where('id', '<', $kladr->id)->first();
            if(!$existingKladr){
                $kladr->slug = $newSlug;
                $kladr->save();
                Log::info('Новый slug1 '.$newSlug.' для : '.json_encode($kladr));
                continue;
            }
            if(!$kladr->region){
                Log::info('Нет региона '.json_encode($kladr));
                // PROBLEM
                continue;
            }
            $newSlug = $newSlug.'-'.SlugService::create($kladr->region);
            $kladr->slug = $newSlug;
            $kladr->save();
            Log::info('Новый slug2 '.$newSlug.' для : '.json_encode($kladr));            
        }
    }


    public static function unifyArrivalPointsSlugs(){
        $dispatchPoints = DispatchPoint::all();
        foreach($dispatchPoints as $dispatchPoint){
            $arrivalPoints = CacheArrivalPoint::where([['dispatch_point_id', '=', $dispatchPoint->id]])->get();
            foreach($arrivalPoints as $arrivalPoint){
                $existingPoint = $arrivalPoints->where('slug', '=', $arrivalPoint->slug)->where('id', '<', $arrivalPoint->id)->first();
                if(!$existingPoint){
                    continue;
                }
                Log::info('Кандидат '.json_encode($existingPoint));
                if(!$existingPoint->details){
                    Log::info('Нет района '.json_encode($existingPoint));
                    // PROBLEM
                    continue;
                }
                $newSlug = $existingPoint->slug.'-'.SlugService::create($existingPoint->details);
                $existingPoint = $arrivalPoints->where('slug', '=', $newSlug)->where('id', '<', $arrivalPoint->id)->first();
                if(!$existingPoint){
                    $arrivalPoint->slug = $newSlug;
                    $arrivalPoint->save();
                    Log::info('Новый slug1 '.$newSlug.' для : '.json_encode($arrivalPoint));
                    continue;
                }
                if(!$arrivalPoint->region){
                    Log::info('Нет региона '.json_encode($arrivalPoint));
                    // PROBLEM
                    continue;
                }
                $newSlug = $newSlug.'-'.SlugService::create($arrivalPoint->region);
                $arrivalPoint->slug = $newSlug;
                $arrivalPoint->save();
                Log::info('Новый slug2 '.$newSlug.' для : '.json_encode($arrivalPoint)); 
            }
        }
    }

    public static function unifySlug(){
        $stations = Station::all();
        foreach($stations as $station){

        }

        $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();
        foreach($kladrs as $kladr){

        }

        $dispatchPoints = DispatchPoint::all();
        foreach($dispatchPoints as $dispatchPoint){
            $arrivalPoints = CacheArrivalPoint::where([['dispatch_point_id', '=', $dispatchPoint->id]])->get();
            foreach($arrivalPoints as $arrivalPoint){

            }
        }
    }
}