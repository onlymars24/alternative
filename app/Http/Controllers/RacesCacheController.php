<?php

namespace App\Http\Controllers;

use App\Models\CacheRace;
use Illuminate\Http\Request;

class RacesCacheController extends Controller
{
    public function get(Request $request){
        $cacheRaces = CacheRace::where([
                ['dispatchPointName', $request->dispatchPointName],
                ['arrivalPointName', $request->arrivalPointName],
                ['date', $request->date]
            ])->first();
        return response([
            'cacheRaces' => $cacheRaces ? json_decode($cacheRaces->list) : []
        ]);
    }
}