<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\BusStation;
use Illuminate\Http\Request;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\File;

class BusStationController extends Controller
{
    public function main(){
        return response([
            'busStationsMain' => json_decode(Setting::where('name', 'busStationsMain')->first()->data)
        ]);
    }
    public function all(Request $request){
        $busStations = BusStation::latest('id')->get();
        return response([
            'busStations' => $busStations
        ]);
    }
    public function one(Request $request){
        $busStation = BusStation::where([['title', $request->title], ['hidden', false]])->first();
        return response([
            'station' => $busStation
        ]);
    }

    public function oneById(Request $request){
        $busStation = BusStation::find($request->id);
        return response([
            'station' => $busStation
        ]);
    }

    public function create(Request $request){
        $busStation = BusStation::create([
            'title' => $request->title,
            'name' => $request->name,
            'description' => $request->description,
            'dispatch_point_id' => $request->dispatch_point_id,
            'kladr_id' => $request->kladr_id,
            'hidden' => $request->hidden,
        ]);

        $newLoc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        for($i = 0; $i < count($xml->url); $i++){
            $xml->url[$i]->lastmod = date('Y-m-d');
        }
        for($i = 0; $i < count($xml->url); $i++){
            if($xml->url[$i]->loc == $newLoc){
                File::put(env('XML_FILE_NAME'), $xml->asXML());
                FtpLoadingService::put();
                return response([
                    'existing' => true
                ]);
            }
        }
        $newNode = $xml->addChild('url');
        $newNode->addChild('loc', $newLoc);
        $newNode->addChild('lastmod', date('Y-m-d'));
        $newNode->addChild('changefreq', 'weekly');
        $newNode->addChild('priority', '1.0');
        
        File::put(env('XML_FILE_NAME'), $xml->asXML());
        FtpLoadingService::put();
        return response([
            'existing' => false
        ]);
    }

    public function delete(Request $request){
        $busStation = BusStation::find($request->id);
        $busStation->delete();
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        for($i = 0; $i < count($xml->url); $i++){
            // dd($xml->url[$i]['id']);
            if((string)$xml->url[$i]->loc == env('FRONTEND_URL').'/автовокзал/'.$busStation->title){
                unset($xml->url[$i]);
                File::put(env('XML_FILE_NAME'), $xml->asXML());
                FtpLoadingService::put();
                return response([
                    'existing' => true
                ]);
            }
        }
        
    }

    public function edit(Request $request){
        // return response(['request' => $request->all() ]);
        $busStation = BusStation::find($request->id);
        $oldLoc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;

        $busStation->title = $request->title;
        $busStation->name = $request->name;
        $busStation->description = $request->description;
        $busStation->hidden = $request->hidden;
        $busStation->dispatch_point_id = $request->dispatch_point_id;
        $busStation->kladr_id = $request->kladr_id;
        $busStation->data = json_encode(['content' => $request->content ? $request->content : '']);
        $busStation->save();

        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        
        for($i = 0; $i < count($xml->url); $i++){
            if((string)$xml->url[$i]->loc == $oldLoc){
                $xml->url[$i]->loc = env('FRONTEND_URL').'/автовокзал/'.$busStation->title;
                $xml->url[$i]->lastmod = date('Y-m-d');
                File::put(env('XML_FILE_NAME'), $xml->asXML());
                FtpLoadingService::put();
                return response([
                    'existing' => true
                ]);
            }
        }
    }
}