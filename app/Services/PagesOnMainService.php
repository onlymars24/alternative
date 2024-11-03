<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Enums\FermaEnum;
use App\Models\KladrStationPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PagesOnMainService
{
    public static function recreate(){
        
        $pagesOnMainSetting = [];

        $kladrStationPages = KladrStationPage::with('kladr')->where([['kladr_id', '<>', null]])->get();
        foreach($kladrStationPages as $kladrStationPage){
            $kladrStationPageArr = $kladrStationPage->toArray();
            $kladrStationPageArr['stationPages'] = KladrStationPage::with('station.kladr')->whereHas('station', function($query) use($kladrStationPage){
                $query->where('kladr_id', '=', $kladrStationPage->kladr_id);
            })->orderByDesc('id')->get();
            $pagesOnMainSetting[$kladrStationPage->kladr->region ? $kladrStationPage->kladr->region : 'Московская обл' ][] = $kladrStationPageArr;
        }

        foreach($pagesOnMainSetting as $key => $region){
          usort($region, function($a, $b) {
            return strcmp($a['name'], $b['name']);
          });
          $pagesOnMainSetting[$key] = $region;
        }
        ksort($pagesOnMainSetting);
        
        $pagesOnMain = Setting::where('name', 'pagesOnMain')->first();
        $pagesOnMain->data = json_encode(json_decode(json_encode($pagesOnMainSetting)));
        $pagesOnMain->save();

        // dd(json_decode($pagesOnMain->data));
    }
}