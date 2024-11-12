<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Enums\FermaEnum;
use App\Models\CacheRace;
use App\Models\DispatchPoint;
use App\Services\PointService;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RaceService
{

    public static function all($request, $date){
        // Log::info($request->dispatchPointId);
        $races = [];
        $dispatchEPoints = [];
        $arrivalEPoints = [];

        $dispatchPointName = null;
        $arrivalPointName = null;

        if($request->dispatchPointType == 'e'){
            // Log::info('etraffic');
            $dispatchEPoint = DispatchPoint::find($request->dispatchPointId);
            $dispatchPointName = $dispatchEPoint->name;
            $dispatchEPoints[] = $dispatchEPoint;
        }
        elseif($request->dispatchPointType == 'k'){
            // Log::info('kladr');
            $dispatchKladr = Kladr::find($request->dispatchPointId);
            $dispatchPointName = $dispatchKladr->name;
            $dispatchEPoints = $dispatchKladr->dispatchPoints;
        }

        if($request->arrivalPointType == 'e'){
            $arrivalEPoint = CacheArrivalPoint::find($request->arrivalPointId);
            $arrivalPointName = $arrivalEPoint->name;
            $arrivalEPoints[] = $arrivalEPoint;
        }
        elseif($request->arrivalPointType == 'k'){
            $arrivalKladr = Kladr::find($request->arrivalPointId);
            $arrivalPointName = $arrivalKladr->name;
            $arrivalEPoints = $arrivalKladr->arrivalPoints;
        }


        $isServerError = false;
        foreach($dispatchEPoints as $dispatchPoint){
            foreach($arrivalEPoints as $arrivalPoint){
                if($arrivalPoint->dispatch_point_id == $dispatchPoint->id){
                    $tempRaces = Http::withHeaders([
                        'Authorization' => env('AVTO_SERVICE_KEY'),
                    ])->get(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$date)->object();

                    if(gettype($tempRaces) == 'array'){
                        // $tempRaces = (array)$tempRaces;
                        // return $tempRaces;
                        
                        
                        foreach($tempRaces as $race){
                            $race->dispatch_point_id = $dispatchPoint->id;
                            $race->arrival_point_id = $arrivalPoint->arrival_point_id;
                            $races[$race->uid] = $race;
                        }
                        // $races = array_merge($tempRaces, $races);
                    }
                    else{
                        $isServerError = true;
                        MailService::sendError(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$date, $tempRaces);
                    }



                    // return response(['races' => $tempRaces]);

                }
            }
        }

        $cacheRace = CacheRace::where([
            ['dispatchPointName', $dispatchPointName],
            ['arrivalPointName', $arrivalPointName],
            ['date', $date]
        ])->first();

        if($cacheRace){
            $cacheRace->list = json_encode($races);
            $cacheRace->save();
        }
        else{
            $cacheRace = CacheRace::create([
                'date' => $date,
                'dispatchPointName' => $dispatchPointName,
                'arrivalPointName' => $arrivalPointName,
                'list' => json_encode($races)
            ]);
        }

        foreach($races as $race){
            $dispatchPointRegion = DispatchPoint::find($dispatchEPoints[0]->id);
            $region = null;
            if(!$dispatchPointRegion || !$dispatchPointRegion->kladr){
                continue;
            }
            $region = $dispatchPointRegion->region;
            $kladrDispatch = $dispatchPointRegion->kladr;
            if(!DispatchPoint::where([['region', '=', $region], ['name', '=', $race->dispatchStationName]])->first()
               && !DispatchPoint::find($race->dispatchPointId)
            ){
                // Log::info('Повод для новых рейсов');
                $dispatchPoint = DispatchPoint::create([
                    'id' => $race->dispatchPointId,
                    'name' => $race->dispatchStationName,
                    'slug' => SlugService::create($race->dispatchStationName),
                    'region' => $region,
                    'okato' => 1,
                    'place' => 1,
                    'kladr_id' => $kladrDispatch->id
                ]);
                PointService::addNewDispatchPoint($dispatchPoint);
            }
        }

        return ['isServerError' => $isServerError, 'races' => $races];
    }
}