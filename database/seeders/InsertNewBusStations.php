<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\DispatchPoint;
use App\Services\BusStationService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertNewBusStations extends Seeder
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
            if(!$kladr->busStation){
                BusStationService::createByKladr($kladr->id);
            }
        }

        foreach($dispatchPoints as $dispatchPoint){
            if(!$dispatchPoint->busStation){
                BusStationService::createByDispatchPoint($dispatchPoint->id);
            }
        }
    }
}
