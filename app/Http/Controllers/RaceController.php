<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}