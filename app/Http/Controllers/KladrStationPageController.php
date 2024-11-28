<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use App\Models\Setting;
use App\Models\Station;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\SlugService;
use App\Models\KladrStationPage;
use App\Services\SitemapService;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\File;

class KladrStationPageController extends Controller
{
    public function main(){
        return response([
            'busStationsMain' => json_decode(Setting::where('name', 'busStationsMain')->first()->data)
        ]);
    }

    public function all(Request $request){
        $busStations = KladrStationPage::latest('id')->get();
        return response([
            'busStations' => $busStations
        ]);
    }

    public function oneOld(Request $request){
        return response([ 'page' => KladrStationPage::where([
            ['url_settlement_name', $request->url_settlement_name],
            ['hidden', false],
            ['station_id', '<>', null]
        ])->first()]);
    }

    public function one(Request $request){
        $arrayPageType = [];
        if($request->pageType == 's'){
            $arrayPageType = ['station_id', '<>', null];
        }
        elseif($request->pageType == 'k'){
            $arrayPageType = ['kladr_id', '<>', null];
        }
        $page = KladrStationPage::with('station.dispatchPoints', 'station.kladr.kladrStationPage', 'kladr')->where([
            ['url_region_code', $request->url_region_code], 
            ['url_settlement_name', $request->url_settlement_name],
            ['hidden', false],
            $arrayPageType
        ])->first();
        return response([
            'page' => $page
        ]);
    }

    public function oneById(Request $request){
        $page = KladrStationPage::with('kladr', 'station.kladr')->find($request->id);
        return response([
            'page' => $page
        ]);
    }

    public function create(Request $request){
        // return response(['station' => $request->all()]);
        $pageArray = [
            'hidden' => $request->booleanHidden,
            // 'address' => $request->address,
            // 'latitude' => $request->latitude,
            // 'longitude' => $request->longitude,
        ];
        $newLocType = '';
        $kladr = null;
        if($request->station_id){
            $station = Station::find($request->station_id);
            $kladr = $station->kladr;
            if(!$kladr){
                return response([
                    'error' => 'Нет связки'
                ]);
            }
            $pageArray['url_settlement_name'] = SlugService::create($station->name);
            $pageArray['name'] = 'Автовокзал '.$station->name;
            $pageArray['description'] = 'Автовокзал '.$station->name.': расписание, справочная, билеты на автобус';
            $pageArray['station_id'] = $request->station_id;
            $newLocType = 'автовокзал';
        }
        elseif($request->kladr_id){
            $kladr = Kladr::find($request->kladr_id);
            $pageArray['url_settlement_name'] = SlugService::create($kladr->name);
            $pageArray['name'] = $kladr->name.' Автовокзалы и автостанции';
            $pageArray['description'] = $kladr->name.' Автовокзалы и автостанции: расписание, справочная, билеты на автобус';
            $pageArray['kladr_id'] = $request->kladr_id;
            $newLocType = 'расписание';
        }
        else{
            return response([
                'error' => 'Нет связки'
            ]);
        }

        $pageArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
        $page = KladrStationPage::create($pageArray);
        SitemapService::addOne(env('FRONTEND_URL').'/'.$newLocType.'/'.$page->url_region_code.'/'.$page->url_settlement_name, 'weekly');
        // $newLoc = env('FRONTEND_URL').'/'.$newLocType.'/'.$busStation->title;
        // $xml = simplexml_load_file(env('XML_FILE_NAME'));
        // for($i = 0; $i < count($xml->url); $i++){
        //     $xml->url[$i]->lastmod = date('Y-m-d');
        // }
        // for($i = 0; $i < count($xml->url); $i++){
        //     if($xml->url[$i]->loc == $newLoc){
        //         File::put(env('XML_FILE_NAME'), $xml->asXML());
        //         FtpLoadingService::put();
        //         return response([
        //             'existing' => true
        //         ]);
        //     }
        // }
        // $newNode = $xml->addChild('url');
        // $newNode->addChild('loc', $newLoc);
        // $newNode->addChild('lastmod', date('Y-m-d'));
        // $newNode->addChild('changefreq', 'weekly');
        // $newNode->addChild('priority', '1.0');
        
        // File::put(env('XML_FILE_NAME'), $xml->asXML());
        // FtpLoadingService::put();
        // return response([
        //     'existing' => false
        // ]);
        return response(['page' => $page]);
    }

    public function delete(Request $request){
        $page = KladrStationPage::find($request->id);


        $locType = $page->station_id ? 'автовокзал' : 'расписание';
        // SitemapService::delete(
        //     env('FRONTEND_URL').'/'.$locType.'/'.$page->url_region_code.'/'.$page->url_settlement_name
        // );

        $page->delete();
        // $xml = simplexml_load_file(env('XML_FILE_NAME'));
        // for($i = 0; $i < count($xml->url); $i++){
        //     // dd($xml->url[$i]['id']);
        //     if((string)$xml->url[$i]->loc == env('FRONTEND_URL').'/автовокзал/'.$busStation->title){
        //         unset($xml->url[$i]);
        //         File::put(env('XML_FILE_NAME'), $xml->asXML());
        //         FtpLoadingService::put();
        //         return response([
        //             'existing' => true
        //         ]);
        //     }
        // }
    }

    public function edit(Request $request){
        // return response(['request' => $request->all() ]);
        $page = KladrStationPage::find($request->id);
        // $oldLoc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;

        // $busStation->title = $request->title;
        $locType = $page->station_id ? 'автовокзал' : 'расписание';
        // SitemapService::edit(
        //     env('FRONTEND_URL').'/'.$locType.'/'.$page->url_region_code.'/'.$page->url_settlement_name
        // );
        $page->name = $request->name;
        $page->description = $request->description;
        $page->hidden = $request->booleanHidden;
        $page->content = $request->content;
        $page->contacts = $request->contacts;
        $page->map = $request->map;

        // $page->address = $request->address;
        // $page->latitude = $request->latitude;
        // $page->longitude = $request->longitude;

        $page->save();
        
        // $xml = simplexml_load_file(env('XML_FILE_NAME'));
        
        // for($i = 0; $i < count($xml->url); $i++){
        //     if((string)$xml->url[$i]->loc == $oldLoc){
        //         $xml->url[$i]->loc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;
        //         $xml->url[$i]->lastmod = date('Y-m-d');
        //         File::put(env('XML_FILE_NAME'), $xml->asXML());
        //         FtpLoadingService::put();
        //         return response([
        //             'existing' => true
        //         ]);
        //     }
        // }
    }

    // busStationsKladrs
    public function kladrPages(){
        return response(['pages' => KladrStationPage::with('kladr')->where([['kladr_id', '<>', null]])->orderByDesc('id')->get()]);
    }

    // busStationsDispatchPoints
    public function stationPages(Request $request){
        // return response(['kladr_id' => $request->kladrId]);
        return response(['pages' => KladrStationPage::with('station.kladr', 'station.dispatchPoints')->whereHas('station', function($query) use($request){
                $query->where('kladr_id', '=', $request->kladrId);
            })->orderByDesc('id')->get()
        ]);
    }

    public function imageUpload(Request $request){
        if(!$request->file('image')){
            return response()->json(['error' => 'Image upload failed'], 400);
        }
        $page = KladrStationPage::find($request->pageId);
        $filePath = $page->header_img;
        if ($filePath && File::exists($filePath)) {
            File::delete($filePath);
            $page->header_img = null;
            $page->save();
        }
        
        $page->header_img = $request->file('image')->store('headers');
        $page->save();
        return response(['pageId' => $page->id]);        

        

    }

    public function imageDelete(Request $request){
        $page = KladrStationPage::find($request->pageId);
        $filePath = $page->header_img;
        if ($filePath && File::exists($filePath)) {
            File::delete($filePath);
            $page->header_img = null;
            $page->save();
        }
    }
}