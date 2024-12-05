<?php

namespace App\Services;

use App\Models\Station;
use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StationService
{    
    public static function connectPointIntoStation($point){
        if(!$point->kladr_id){
            return null;
        }
        $station = Station::where([['name', '=', $point->name], ['kladr_id', '=', $point->kladr_id]])->first();
        if(!$station){
            $station = Station::create([
                'name' => $point->name,
                'slug' => SlugService::create($point->name),
                'kladr_id' => $point->kladr_id
            ]);
            $station->sourceId = 'stations-'.$station->id;
            $station->save();  
        }
      
        return $station->id;
    }
}