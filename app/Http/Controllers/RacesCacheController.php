<?php

namespace App\Http\Controllers;

use App\Models\CacheRace;
use Illuminate\Http\Request;

class RacesCacheController extends Controller
{
    public function get(Request $request){
        // return response([
        //     'cacheRaces1' => $request->dispatchSlug,
        //     'cacheRaces2' => $request->arrivalSlug,
        //     'cacheRaces3' => $request->date,
        // ]);
        $cacheRaces = CacheRace::where([
                ['dispatchPointName', $request->dispatchSlug],
                ['arrivalPointName', $request->arrivalSlug],
                ['date', $request->date]
            ])->first();
        return response([
            'cacheRaces' => $cacheRaces ? json_decode($cacheRaces->list) : []
        ]);
    }
}