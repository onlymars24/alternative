<?php

namespace App\Services;

use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SitemapService
{    
    public static function add($newLoc, $changefreq){
        Log::info('sitemap2');
        $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        for($i = 0; $i < count($xml->url); $i++){
            Log::info($xml->url[$i]->loc.' '.$newLoc);
            if($xml->url[$i]->loc == $newLoc){
                return [
                    'existing' => true
                ];
            }
        }
        $newNode = $xml->addChild('url');
        $newNode->addChild('loc', $newLoc);
        $newNode->addChild('lastmod', date('Y-m-d'));
        $newNode->addChild('changefreq', $changefreq); //weekly
        $newNode->addChild('priority', '1.0');
        
        File::put(env('XML_FILE_NAME'), $xml->asXML());
        Log::info('sitemap3');
        FtpLoadingService::put();
    }

    public static function edit($loc){
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        
        for($i = 0; $i < count($xml->url); $i++){
            if((string)$xml->url[$i]->loc == $loc){
                $xml->url[$i]->lastmod = date('Y-m-d');
                File::put(env('XML_FILE_NAME'), $xml->asXML());
                FtpLoadingService::put();
                return [
                    'existing' => true
                ];
            }
        }
    }

    public static function delete($loc){
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        for($i = 0; $i < count($xml->url); $i++){
            // dd($xml->url[$i]['id']);
            if((string)$xml->url[$i]->loc == $loc){
                unset($xml->url[$i]);
                File::put(env('XML_FILE_NAME'), $xml->asXML());
                FtpLoadingService::put();
                return [
                    'existing' => true
                ];
            }
        }
    }
}