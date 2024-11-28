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

    public static function unifySlug(){
        // $stations = Station::all();
        // foreach($stations as $station){
        //     $existingStation = Station::where([['slug', '=', $station->slug], ['id', '<', $station->id]])->first();
        //     if(!$existingStation){
        //         continue;
        //     }
        //     if(isset($station->kladr) && $station->kladr->district){
        //         $newSlug = $station->slug.'-'.$station->kladr;
        //         $existingStation = Station::where([['slug', '=', $newSlug], ['id', '<', $station->id]])->first();
        //     }
        // }

        // $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();

        // $dispatchPoints = DispatchPoint::all();

        // foreach($dispatchPoints as $dispatchPoint){
        //     $arrivalPoints = CacheArrivalPoint::where([['dispatch_point_id', '=', $dispatchPoint->id]])->get();
        //     foreach($arrivalPoints as $arrivalPoint){

        //     }
        // }
    }
}