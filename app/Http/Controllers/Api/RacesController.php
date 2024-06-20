<?php

namespace App\Http\Controllers\Api;

use App\Models\CacheRace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class RacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($date, Request $request)
    {
        $races = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/races/'.$request->dispatch_point_id.'/'.$request->arrival_point_id.'/'.$date)->object();
        Log::info(json_encode($request->all()));

        $cacheRace = CacheRace::where([
            ['dispatchPointName', $request->dispatch_point_name],
            ['arrivalPointName', $request->arrival_point_name],
            ['date', $date]
        ])->first();

        if($cacheRace){
            $cacheRace->list = json_encode($races);
            $cacheRace->save();
        }
        else{
            $cacheRace = CacheRace::create([
                'date' => $date,
                'dispatchPointName' => $request->dispatch_point_name,
                'arrivalPointName' => $request->arrival_point_name,
                'list' => json_encode($races)
            ]);            
        }


        return json_encode($races);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
