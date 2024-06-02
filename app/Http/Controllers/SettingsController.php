<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{

    public function getDues(){
        $setting = Setting::where('name', 'dues')->first()->data;
        $dues = json_decode($setting);
        return response([
            'dues' => $dues
        ]);
    }


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

    public function setDue(Request $request){
        $setting = Setting::where('name', 'dues')->first();
        $dues = (array)json_decode($setting->data);
        $dues[$request->name] = $request->percent;
        $setting->data = json_encode($dues);
        $setting->save();
        return response([
            'setting' => $setting
        ]);
    }

    public function getBonusesPercent(){
        $setting = Setting::where('name', 'bonusesPercent')->first()->data;
        $setting = (array)json_decode($setting);
        return response([
            'bonusesPercent' => $setting['bonusesPercent']
        ]);
    }
}