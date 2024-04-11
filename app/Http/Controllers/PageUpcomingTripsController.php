<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PageUpcomingTripsController extends Controller
{
    public function get(){
        $pageUpcomingTrips = Setting::where('name', 'pageUpcomingTrips')->first();
        $pageUpcomingTrips = json_decode($pageUpcomingTrips->data);
        return response([
            'pageUpcomingTrips' => $pageUpcomingTrips->content
        ]);
    }

    public function edit(Request $request){
        $pageUpcomingTrips = Setting::where('name', 'pageUpcomingTrips')->first();
        $data = json_decode($pageUpcomingTrips->data);
        $data->content = $request->content;
        $pageUpcomingTrips->data = json_encode($data);
        $pageUpcomingTrips->save();
        return response([
            'pageUpcomingTrips' => $pageUpcomingTrips
        ]);
    }
}
