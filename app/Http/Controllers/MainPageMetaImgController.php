<?php

namespace App\Http\Controllers;

use App\MyApp;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MainPageMetaImgController extends Controller
{
    // public function getImgName(){
    //     $setting = Setting::where('name', 'pageMain')->first();
    //     return (array)json_decode($setting->data)['metaImg'];
    // }

    public function get(){
        $setting = Setting::where('name', 'pageMain')->first();
        $metaImg = json_decode($setting->data)->metaImg;
        return response([
            'metaImg' => $metaImg == MyApp::META_IMG_DEFAULT ? null : $metaImg
        ]);
    }
    
    public function upload(Request $request){
        $validator = Validator::make($request->all(), [
            'metaImg' => 'required|image',
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }

        $metaImgName = $request->file('metaImg')->store('meta');

        $setting = Setting::where('name', 'pageMain')->first();
        $data = json_decode($setting->data);
        $metaImgOldName = $data->metaImg;

        if($metaImgOldName && $metaImgOldName != MyApp::META_IMG_DEFAULT && File::exists($metaImgOldName)){
            File::delete($metaImgOldName);
        }
        $data->metaImg = $metaImgName;
        $setting->data = json_encode($data);
        $setting->save();
    }

    public function delete(){
        $setting = Setting::where('name', 'pageMain')->first();
        $data = json_decode($setting->data);
        $metaImgOldName = $data->metaImg;
        if($metaImgOldName && $metaImgOldName != MyApp::META_IMG_DEFAULT && File::exists($metaImgOldName)){
            File::delete($metaImgOldName);
        }
        $data->metaImg = MyApp::META_IMG_DEFAULT;
        $setting->data = json_encode($data);
        $setting->save();
    }
}