<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Models\Station;
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
        // ПОТОМ РАСКОМЕНТЬ!!!!!!!!!!
        // if($date < date('Y-m-d')){
        //     // Log::info(json_encode($request->ips()));
        //     // MailService::sendError('Для url: '.$request->url.' выполнился запрос задним числом '.$date.' Ip: '.$request->userData, $date);
        //     return ['isServerError' => false, 'races' => []];
        // }
        // MailService::sendError('Нормальный запрос от url: '.$request->url.' Ip: '.$request->userData, $date);
        // Log::info($request->dispatchPointId);
        $races = [];
        $dispatchEPoints = [];
        $arrivalEPoints = [];

        $dispatchSlug = null;
        $arrivalSlug = null;

        $dispatchSourceId = explode('-', $request->dispatchSourceId);

        if($dispatchSourceId[0] == 'kladrs'){
            $kladr = Kladr::find($dispatchSourceId[1]);
            $dispatchSlug = $kladr->slug;
            $stations = $kladr->stations;
            foreach($stations as $station){
                foreach($station->dispatchPoints as $dispatchPoint){
                    $dispatchEPoints[] = $dispatchPoint;
                }
            }
        }

        if($dispatchSourceId[0] == 'stations'){
            $station = Station::find($dispatchSourceId[1]);
            $dispatchSlug = $station->slug;
            $dispatchEPoints = $station->dispatchPoints;
        }


        $arrivalSourceId = explode('-', $request->arrivalSourceId);

        if($arrivalSourceId[0] == 'kladrs'){
            $kladr = Kladr::find($arrivalSourceId[1]);
            $arrivalSlug = $kladr->slug;
            $stations = $kladr->stations;
            foreach($stations as $station){
                foreach($station->arrivalPoints as $arrivalPoint){
                    $arrivalEPoints[] = $arrivalPoint;
                }
            }
        }

        if($arrivalSourceId[0] == 'stations'){
            $station = Station::find($arrivalSourceId[1]);
            $arrivalSlug = $station->slug;
            $arrivalEPoints = $station->arrivalPoints;
        }

        if($arrivalSourceId[0] == 'cache_arrival_points'){
            $cacheArrivalPoint = CacheArrivalPoint::find($arrivalSourceId[1]);
            $arrivalSlug = $cacheArrivalPoint->slug;
            $arrivalEPoints[] = $cacheArrivalPoint;
        }


        $isServerError = false;
        foreach($dispatchEPoints as $dispatchPoint){
            foreach($arrivalEPoints as $arrivalPoint){
                if($arrivalPoint->dispatch_point_id == $dispatchPoint->id){
                    $tempRaces = Http::withHeaders([
                        'Authorization' => env('AVTO_SERVICE_KEY'),
                    ])->get(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$date)->object();

                    if(gettype($tempRaces) == 'array'){
                        foreach($tempRaces as $race){
                            $race->dispatch_point_id = $dispatchPoint->id;
                            $race->arrival_point_id = $arrivalPoint->arrival_point_id;
                            $races[$race->uid] = $race;
                        }
                    }
                    // else{
                    //     $isServerError = true;
                    //     MailService::sendError(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$date.' || '.$request->url, $tempRaces);
                    // }



                    // return response(['races' => $tempRaces]);

                }
            }
        }

        $cacheRace = CacheRace::where([
            ['dispatchPointName', $dispatchSlug],
            ['arrivalPointName', $arrivalSlug],
            ['date', $date]
        ])->first();

        if($cacheRace){
            $cacheRace->list = json_encode($races);
            $cacheRace->save();
        }
        else{
            $cacheRace = CacheRace::create([
                'date' => $date,
                'dispatchPointName' => $dispatchSlug,
                'arrivalPointName' => $arrivalSlug,
                'list' => json_encode($races)
            ]);
        }

        // foreach($races as $race){
        //     $dispatchPointRegion = DispatchPoint::find($dispatchEPoints[0]->id);
        //     $region = null;
        //     if(!$dispatchPointRegion || !$dispatchPointRegion->kladr){
        //         continue;
        //     }
        //     $region = $dispatchPointRegion->region;
        //     $kladrDispatch = $dispatchPointRegion->kladr;
        //     if(!DispatchPoint::where([['region', '=', $region], ['name', '=', $race->dispatchStationName]])->first()
        //        && !DispatchPoint::find($race->dispatchPointId)
        //     ){
        //         // Log::info('Повод для новых рейсов');
        //         $dispatchPoint = DispatchPoint::create([
        //             'id' => $race->dispatchPointId,
        //             'name' => $race->dispatchStationName,
        //             'slug' => SlugService::create($race->dispatchStationName),
        //             'region' => $region,
        //             'okato' => 1,
        //             'place' => 1,
        //             'kladr_id' => $kladrDispatch->id
        //         ]);
        //         PointService::addNewArrivalPoints($dispatchPoint);
        //     }
        // }

        return $races;
    }
}