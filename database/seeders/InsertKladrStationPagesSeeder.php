<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\Station;
use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Models\KladrStationPage;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertKladrStationPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispatchPoints = DispatchPoint::all();
        $kladrs = Kladr::has('dispatchPoints')->get();

        foreach($kladrs as $kladr){
            if(!$kladr->kladrStationPage){
                $pageArray = ['hidden' => false];
                $pageArray['url_settlement_name'] = str_replace([' ', '/'], ['_', '-'], $kladr->name);
                $pageArray['name'] = $kladr->name.' Автовокзалы и автостанции';
                $pageArray['description'] = $kladr->name.' Автовокзалы и автостанции: расписание, справочная, билеты на автобус';
                $pageArray['kladr_id'] = $kladr->id;
                $newLocType = 'расписание';
                $pageArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
                $page = KladrStationPage::create($pageArray);
                SitemapService::add(env('FRONTEND_URL').'/'.$newLocType.'/'.$page->url_region_code.'/'.$page->url_settlement_name, 'weekly');
            }
        }

        foreach($dispatchPoints as $dispatchPoint){
            $pageArray = ['hidden' => false];
            if(!$dispatchPoint->station){
                $station = Station::create([
                    'name' => $dispatchPoint->name,
                    'address' => $dispatchPoint->address,
                    'longitude' => $dispatchPoint->longitude,
                    'latitude' => $dispatchPoint->latitude,
                    'kladr_id' => $dispatchPoint->kladr_id
                ]);    
                $dispatchPoint->station_id = $station->id;
                $dispatchPoint->save();       
            }
            else{
                $station = $dispatchPoint->station;
            }
            if(!$station->kladrStationPage){
                $kladr = $station->kladr;
                if(!$kladr){
                    continue;
                }
                $pageArray['url_settlement_name'] = str_replace([' ', '/'], ['_', '-'], $station->name);
                $pageArray['name'] = 'Автовокзал '.$station->name;
                $pageArray['description'] = 'Автовокзал '.$station->name.': расписание, справочная, билеты на автобус';
                $pageArray['station_id'] = $station->id;
                $pageArray['url_region_code'] = mb_strcut($kladr->code, 0, 2);
                $page = KladrStationPage::create($pageArray);
                $newLocType = 'автовокзал';
                Log::info('sitemap1');
                SitemapService::add(env('FRONTEND_URL').'/'.$newLocType.'/'.$page->url_region_code.'/'.$page->url_settlement_name, 'weekly');
        
                
            }
        }
    }
}
