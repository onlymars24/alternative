<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\Station;
use App\Models\DispatchPoint;
use App\Services\SlugService;
use Illuminate\Database\Seeder;
use App\Models\KladrStationPage;
use App\Models\CacheArrivalPoint;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $pages = KladrStationPage::with('station', 'kladr')->get();
        // foreach($pages as $item){
        //     $item->url_settlement_name = SlugService::create($item->url_settlement_name);
        //     $item->save();
        // }

        // $dispatchPoints = DispatchPoint::all();
        // foreach($dispatchPoints as $item){
        //     $item->slug = SlugService::create($item->name);
        //     $item->save();
        // }

        $stations = Station::all();
        foreach($stations as $item){
            $item->slug = SlugService::create($item->name);
            $item->save();
        }

        $arrivalPoints = CacheArrivalPoint::all();
        foreach($arrivalPoints as $item){
            $item->slug = SlugService::create($item->name);
            $item->save();
        }

        $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->with('kladrStationPage')->get();
        foreach($kladrs as $item){
            $item->slug = SlugService::create($item->name);
            $item->save();
        }      
    }
}
