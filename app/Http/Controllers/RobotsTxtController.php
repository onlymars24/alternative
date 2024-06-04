<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RobotsTxtController extends Controller
{
    public function get(){
        $robotsTxt = Setting::where('name', 'robotsTxt')->first();
        $robotsTxt = json_decode($robotsTxt->data);
        return response([
            'robotsTxt' => $robotsTxt->content
        ]);
    }

    public function edit(Request $request){
        $robotsTxt = Setting::where('name', 'robotsTxt')->first();
        $data = json_decode($robotsTxt->data);
        $data->content = $request->robotsTxt;
        $robotsTxt->data = json_encode($data);
        $robotsTxt->save();
        if(env('APP_ENV') == 'production'){
            $ftp = Storage::disk('sftp')->put('/var/www/rosvokzaly/data/public/robots.txt', $request->robotsTxt);
        }
        return response([
            'robotsTxt' => $robotsTxt
        ]);
    }
}
