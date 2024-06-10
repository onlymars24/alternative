<?php

namespace App\Http\Controllers;

use Nette\Utils\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RaceController extends Controller
{
    public function get(Request $request){
        $races = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/races/'.$request->dispatchPointId.'/'.$request->arrivalPointId.'/'.$request->date)->object();
        // return response(['request'=>$races]);
        if(is_array($races)){
            foreach($races as $race){
                if($race->uid == $request->uid){
                    $raceSummary = Http::withHeaders([
                        'Authorization' => env('AVTO_SERVICE_KEY'),
                    ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$request->uid)->object();
                    return json_encode($raceSummary);
                }
            }            
        }
        return null;
    }

    public function sevenDaysRaces(Request $request){
        $dates = [];
        for($i = 1; $i <= 7; $i++){
            $datetime = new DateTime($request->date);
            $datetime->modify('+'.$i.' day');
            $date = $datetime->format('Y-m-d');    
            $races = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->get(env('AVTO_SERVICE_URL').'/races/'.$request->dispatchPointId.'/'.$request->arrivalPointId.'/'.$date);
            Log::info($races);
            $races = json_decode($races);
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