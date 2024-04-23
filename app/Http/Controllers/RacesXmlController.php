<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Services\FtpLoadingService;

class RacesXmlController extends Controller
{
    //dispatchName
    //arrivalName
    public function create(Request $request){
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        for($i = 0; $i < count($xml->url); $i++){
          $xml->url[$i]->lastmod = date('Y-m-d');
        }
        $newLoc = env('FRONTEND_URL').'/автобус/'.$request->dispatchName.'/'.$request->arrivalName;
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
        $newNode->addChild('changefreq', 'daily');
        $newNode->addChild('priority', '1.0');

        File::put(env('XML_FILE_NAME'), $xml->asXML());
        FtpLoadingService::put();
        
        return response([
            'existing' => false,
            'newXml' => $xml->asXML()
        ]);
    }
}