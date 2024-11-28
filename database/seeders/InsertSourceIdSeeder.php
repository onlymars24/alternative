<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\Station;
use Illuminate\Database\Seeder;
use App\Models\CacheArrivalPoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertSourceIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stations = Station::all();
        foreach($stations as $station){
            $station->sourceId = 'stations-'.$station->id;
            $station->save();
        }

        $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();
        foreach($kladrs as $kladr){
            $kladr->sourceId = 'kladrs-'.$kladr->id;
            $kladr->save();
        }

        $arrivalPoints = CacheArrivalPoint::all();
        foreach($arrivalPoints as $arrivalPoint){
            $arrivalPoint->sourceId = 'cache_arrival_points-'.$arrivalPoint->id;
            $arrivalPoint->save();
        }
    }
}
