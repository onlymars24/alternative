<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Nette\Utils\DateTime;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\MailService;
use App\Services\RaceService;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
                    return json_encode($raceSummary);
                }
            }
        }
        return null;
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
            if($races){
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
}