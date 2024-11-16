<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Services\PointService;
use Illuminate\Database\Seeder;
use App\Models\KladrStationPage;
use App\Services\SitemapService;
use Illuminate\Support\Facades\DB;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RecreateSitemapDbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $xml = simplexml_load_file(public_path('sitemap.local.xml'));

        DB::table('sitemap_pages')->delete();

      
        $pages = KladrStationPage::all();
        foreach($pages as $page){
          $result[] = env('FRONTEND_URL').'/'.($page->kladr_id ? 'расписание' : 'автовокзал').'/'.$page->url_region_code.'/'.$page->url_settlement_name;
        }
        // dd($res);
        // $result = [];
        $dispatchData = PointService::dispatchKandE();
        foreach($dispatchData as $dispatchItem){
          $arrivalData = null;
          if(array_key_exists('details', $dispatchItem)){
            $arrivalData = PointService::kAndE($dispatchItem['id']);
            foreach($arrivalData as $arrivalItem){
              $result[env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug']] = env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug'];
            }
          }
          else{
            // dd(Kladr::find($dispatchItem['id']));
            $kladr = Kladr::find($dispatchItem['id']);
            // if(!$kladr){
            //   dd($kladr, $dispatchItem, $dispatchItem['id']);
            // }
            $dispatchPoints = $kladr->dispatchPoints;
            
            foreach($dispatchPoints as $dispatchPoint){
              $arrivalData = PointService::kAndE($dispatchPoint->id);
              foreach($arrivalData as $arrivalItem){
                $result[env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug']] = env('FRONTEND_URL').'/автобус/'.$dispatchItem['slug'].'/'.$arrivalItem['slug'];
              }
            }
          }
          
          // foreach($arrivalData as $arrivalItem){
          //   
          // }
        }
        // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        foreach($result as $item){
          $xml = SitemapService::add($item, stripos($item, 'автобус') === false ? 'weekly' : 'daily', $xml);
        }
        File::put(public_path(env('XML_FILE_NAME')), $xml->asXML());
        FtpLoadingService::put();
        // dd($xml);  
    }
}
