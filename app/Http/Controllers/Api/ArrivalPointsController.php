<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CacheArrivalPoint;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ArrivalPointsController extends Controller
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
    public function show($id)
    {
        $arrival_points = CacheArrivalPoint::where('dispatch_point_id', $id)->first();
        if($arrival_points){
            return $arrival_points;
        }
        else{
            $arrival_points_remoted = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$id)->object();
            $arrival_points = CacheArrivalPoint::create([
                'dispatch_point_id' => $id,
                'arrival_points' => json_encode($arrival_points_remoted)
            ]);
            return $arrival_points;
        }
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