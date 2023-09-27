<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function getClusterDue(){
        $setting = Setting::where('name', 'dues')->first()->data;
        $setting = (array)json_decode($setting);
        return response([
            'clusterDue' => $setting['clusterDue']
        ]);
    }

    public function setClusterDue(Request $request){
        $data = ['clusterDue' => $request->percent];
        Setting::create([
            'name' => 'dues',
            'data' => json_encode($data)
        ]);
        return response([
            'message' => 'Great!'
        ]);
    }
}