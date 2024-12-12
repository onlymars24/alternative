<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Nette\Utils\DateTime;
use App\Mail\ErrorApiMail;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\MailService;
use App\Services\RaceService;
use App\Services\SlugService;
use App\Services\PointService;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class RaceController extends Controller
{

    //dispatchPointId
    //dispatchPointType
    //arrivalPointId
    //arrivalPointType
    //date
    public function races(Request $request){
        return response([
            'races' => RaceService::all($request, $request->date)
        ]);
    }

    //dispatchPointId
    //dispatchPointType
    //arrivalPointId
    //arrivalPointType
    //date
    public function sevenDaysRaces(Request $request){
        $dates = [];
        for($i = 1; $i <= 7; $i++){
            $datetime = new DateTime($request->date);
            $datetime->modify('+'.$i.' day');
            $date = $datetime->format('Y-m-d');
            $races = RaceService::all($request, $date); 




            // $races = Http::withHeaders([
            //     'Authorization' => env('AVTO_SERVICE_KEY'),
            // ])->get(env('AVTO_SERVICE_URL').'/races/'.$request->dispatchPointId.'/'.$request->arrivalPointId.'/'.$date);
            // Log::info($races);
            // $races = json_decode($races);
            if(count($races) > 0){
                log::info(json_encode($races));
                return response([
                    'date' => $date
                ]);
            }
            sleep(2);
        }
        return response([
            'date' => null
        ]);
    }
    
    public function simpleRaces(Request $request){
        $isServerError = false;
        $races = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/races/'.$request->dispatchPointId.'/'.$request->arrivalPointId.'/'.$request->date)->object();
        // ИСКУССТВЕННАЯ ПОЛОМКА АВТОВОКЗАЛА
        // $races = json_decode(json_encode([
        //     "errorMessage" => "Автовокзал недоступен: Томск АВ",
        //     "errorType" => "UNAVAILABLE"
        // ]));
        if(is_array($races)){
            foreach($races as $race){
                $race->dispatch_point_id = $request->dispatchPointId;
                $race->arrival_point_id = $request->arrivalPointId;
                $dispatchPointRegion = DispatchPoint::find($request->dispatchPointId);
                $region = null;
                if(!$dispatchPointRegion || !$dispatchPointRegion->kladr){
                    continue;
                }
                $region = $dispatchPointRegion->region;
                $kladrDispatch = $dispatchPointRegion->kladr;
                if(!DispatchPoint::where([['region', '=', $region], ['name', '=', $race->dispatchStationName]])->first()
                   && !DispatchPoint::find($race->dispatchPointId)
                ){
                    $dispatchPoint = DispatchPoint::create([
                        'id' => $race->dispatchPointId,
                        'name' => $race->dispatchStationName,
                        'slug' => SlugService::create($race->dispatchStationName),
                        'region' => $region,
                        'okato' => 1,
                        'place' => 1,
                        'kladr_id' => $kladrDispatch->id
                    ]);
                    PointService::addNewArrivalPoints($dispatchPoint);
                }
            }
            return response(['isServerError' => $isServerError, 'races' => $races]);
        }
        elseif(is_object($races) && isset($races->errorMessage) && stripos($races->errorMessage, 'Автовокзал недоступен') !== false){
            $isServerError = true;
        }
        MailService::sendError(env('AVTO_SERVICE_URL').'/races/'.$request->dispatchPointId.'/'.$request->arrivalPointId.'/'.$request->date.' || '.$request->url, $races);
        return response(['isServerError' => $isServerError, 'races' => []]);
    }

    public function get(Request $request){
        $races = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/races/'.$request->dispatchPointId.'/'.$request->arrivalPointId.'/'.$request->date)->object();

        if(is_array($races)){
            foreach($races as $race){
                if($race->uid == $request->uid){
                    $raceSummary = Http::withHeaders([
                        'Authorization' => env('AVTO_SERVICE_KEY'),
                    ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$request->uid)->object();
                    if(!isset($raceSummary->race->uid)){
                        MailService::sendError(env('AVTO_SERVICE_URL').'/race/summary/'.$request->uid, $raceSummary);
                    }
                    elseif(isset($raceSummary->depot)){
                        $dispatchPoint = DispatchPoint::find($raceSummary->race->dispatchPointId);
                        if($dispatchPoint && $dispatchPoint->station){
                            $station = $dispatchPoint->station;
                            $depot = $raceSummary->depot;
                            if(!$station->depotId){
                                $station->depotId = $depot->id;
                                $station->save();    
                            }
                            if(!$station->address){
                                if($depot->address){
                                    $station->address = stripos($depot->address, $station->kladr->name) ? $depot->address : ($station->kladr->name.', '.$depot->address);
                                    $station->save(); 
                                }
                                if($depot->latitude && $depot->longitude && $depot->address){
                                    $station->latitude = $depot->latitude;
                                    $station->longitude = $depot->longitude;
                                    $station->save();
                                }
                                elseif($depot->address){
                                    $geoCode = Http::get('https://geocode-maps.yandex.ru/1.x/?apikey=e40ec27a-8117-4ad6-9b72-649510a74f02&geocode='.($station->address).'&format=json')->object();
                                    if(isset($geoCode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos)){
                                        $pos = $geoCode->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;
                                        $coordinates = explode(' ', $pos);
                                        $station->latitude = $coordinates[1];
                                        $station->longitude = $coordinates[0];
                                        $station->save();
                                    }
                                }
                                Mail::to(env('ERROR_MAIL_MARSEL'))->send(new ErrorApiMail('Новое местоположение для '.$station->name, $station->toArray()));
                            }
                        }
                    }
                    // return response(['errorMessage' => 'Ошибка при запросе к серверу автовокзала Томск КДП. Места заняты. Тип места: Пассажирские, номер места: 25.'], 500);
                    return json_encode($raceSummary);
                }
            }
        }
        return null;
    }
}