<?php

namespace Database\Seeders;

use App\Services\PointService;
use Illuminate\Database\Seeder;
use App\Models\KladrStationPage;
use App\Services\SitemapService;
use Illuminate\Support\Facades\DB;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SitemapReloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('max_execution_time', 600);

        $sitemapPath = public_path(env('XML_FILE_NAME'));
        File::put($sitemapPath, '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        </urlset>');
        FtpLoadingService::put();
      
        DB::table('sitemap_pages')->delete();
        $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
      
       
      
      
        $pages = KladrStationPage::all();
        foreach($pages as $page){
          $result[] = env('FRONTEND_URL').'/'.($page->kladr_id ? 'расписание' : 'автовокзал').'/'.$page->url_region_code.'/'.$page->url_settlement_name;
        }
        // dd($res);
        // $result = [];
        $dispatchData = PointService::dispatchData();
        foreach($dispatchData as $dispatchItem){
          $arrivalData = PointService::arrivalDataBySourceId($dispatchItem->sourceId);
          foreach($arrivalData as $arrivalItem){
            $result[env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug']] = env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug'];
          }
        }
        // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        foreach($result as $item){
          $xml = SitemapService::add($item, stripos($item, 'автобус') === false ? 'weekly' : 'daily', $xml);
        }
        File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
        FtpLoadingService::put();      
    }
}
