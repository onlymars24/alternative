<?php

namespace App\Services;

use App\Enums\FermaEnum;
use App\Models\SitemapPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SitemapService
{    
    public static function add($newLoc, $changefreq, $xml){
        if(!SitemapPage::where('url', $newLoc)->first()){
            SitemapPage::create([
                'url' => $newLoc,
                'changefreq' => $changefreq
            ]);
        }
        $namespaces = $xml->getNamespaces(true);
        // Если пространство имен существует, добавляем его в XPath запрос
        if (isset($namespaces[''])) {
            // Добавляем пространство имен в XPath запрос
            $xml->registerXPathNamespace('sm', $namespaces['']);
            $url = $xml->xpath("//sm:url[sm:loc='$newLoc']");
        } else {
            // Если пространства имен нет, просто выполняем запрос без него
            $url = $xml->xpath("//url[loc='$newLoc']");
        }  
        if($url){
            return $xml;
        }
        $newNode = $xml->addChild('url');
        $newNode->addChild('loc', $newLoc);
        $newNode->addChild('lastmod', date('Y-m-d'));
        $newNode->addChild('changefreq', $changefreq); //weekly
        $newNode->addChild('priority', '1.0');
        return $xml;
    }

    // public static function edit($loc){
    //     $xml = simplexml_load_file(env('XML_FILE_NAME'));
        
    //     for($i = 0; $i < count($xml->url); $i++){
    //         if((string)$xml->url[$i]->loc == $loc){
    //             $xml->url[$i]->lastmod = date('Y-m-d');
    //             File::put(env('XML_FILE_NAME'), $xml->asXML());
    //             FtpLoadingService::put();
    //             return [
    //                 'existing' => true
    //             ];
    //         }
    //     }
    // }

    // public static function delete($loc){
    //     $xml = simplexml_load_file(env('XML_FILE_NAME'));
    //     for($i = 0; $i < count($xml->url); $i++){
    //         // dd($xml->url[$i]['id']);
    //         if((string)$xml->url[$i]->loc == $loc){
    //             unset($xml->url[$i]);
    //             File::put(env('XML_FILE_NAME'), $xml->asXML());
    //             FtpLoadingService::put();
    //             return [
    //                 'existing' => true
    //             ];
    //         }
    //     }
    // }

    public static function addOne($newLoc, $changefreq){
        $xml = simplexml_load_file(env('XML_FILE_NAME'));
        $xml = self::add($newLoc, $changefreq, $xml);
        File::put(env('XML_FILE_NAME'), $xml->asXML());
        FtpLoadingService::put();
    }
}