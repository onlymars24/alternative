<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Services\AdPdfService;
use App\Services\SettingService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdvertisingPdfController extends Controller
{
    public function get(){
        return response([
            'adPdf' => AdPdfService::get()
        ]);
    }
    
    public function upload(Request $request){
        $validator = Validator::make($request->all(), [
            'adPdf' => 'required|mimes:pdf',
        ]);
        if($validator->fails()){
            return response(
                [
                    'errors' => $validator->errors()
                ], 422
            );
        }
        $newFilePath = $request->file('adPdf')->store('settings');
        if(!AdPdfService::testMergePdf($newFilePath)){
            File::delete($newFilePath);
            return response(
                [
                    'errors' => [
                        'adPdf' => ['Pdf файл не поддерживает объединения c другим pdf файлом']
                    ]
                ], 422
            );
        }
        File::delete('settings/test_merge.pdf');
        $adPdfName = AdPdfService::get();
        if ($adPdfName && File::exists($adPdfName)) {
            File::delete($adPdfName);
        }
        
        $setting = Setting::where('name', 'adPdf')->first();
        $data = (array)json_decode($setting->data);
        $data['adPdf'] = $newFilePath;
        $setting->data = json_encode($data);
        $setting->save();
    }

    public function delete(){
        $setting = Setting::where('name', 'adPdf')->first();
        $data = (array)json_decode($setting->data);
        
        if($data['adPdf'] && File::exists($data['adPdf'])){
            File::delete($data['adPdf']);
        }
        $data['adPdf'] = null;
        $setting->data = json_encode($data);
        $setting->save();
    }
}