<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\KladrStationPage;

class PageMainController extends Controller
{
    public function get(){
        $pageMain = Setting::where('name', 'pageMain')->first();
        $pageMain = json_decode($pageMain->data);
        return response([
            'pageMain' => $pageMain->content
        ]);
    }

    public function edit(Request $request){
        $pageMain = Setting::where('name', 'pageMain')->first();
        $data = json_decode($pageMain->data);
        $data->content = $request->content;
        $pageMain->data = json_encode($data);
        $pageMain->save();
        return response([
            'pageMain' => $pageMain
        ]);
    }

    public function mainPages(){
        $pagesOnMainSetting = [];

        $kladrStationPages = KladrStationPage::with('kladr')->where([['kladr_id', '<>', null], ['hidden', '=', false]])->get();
        foreach($kladrStationPages as $kladrStationPage){
            $kladrStationPageArr = $kladrStationPage->toArray();
            $kladrStationPageArr['stationPages'] = KladrStationPage::with('station.kladr')->whereHas('station', function($query) use($kladrStationPage){
                $query->where([['kladr_id', '=', $kladrStationPage->kladr_id], ['hidden', '=', false]]);
            })->orderByDesc('id')->get()->toArray();
      
            // foreach($pagesOnMainSetting as $key => $region){
                usort($kladrStationPageArr['stationPages'], function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
                // $pagesOnMainSetting[$key] = $region;
            // }
            // ksort($pagesOnMainSetting);
      
            $pagesOnMainSetting[$kladrStationPage->kladr->region ? $kladrStationPage->kladr->region : 'Московская обл' ][] = $kladrStationPageArr;
        }
      
        foreach($pagesOnMainSetting as $key => $region){
            usort($region, function($a, $b) {
            return strcmp($a['name'], $b['name']);
            });
            foreach($region as $key1 => $settlement){
                // dd($settlement);
                if(substr($settlement['kladr']['code'], 5, 3) == '001'){
                    $temp = $settlement;
                    $settlement = $region[0];
                    $region[0] = $temp;
                }
            }
            $pagesOnMainSetting[$key] = $region;
        }

        return response([
            'pagesOnMain' => $pagesOnMainSetting
        ]);
    }    
}