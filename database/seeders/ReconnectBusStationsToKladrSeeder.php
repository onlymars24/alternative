<?php

namespace Database\Seeders;

use App\Models\BusStation;
use App\Services\PointService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReconnectBusStationsToKladrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispatchData = PointService::dispatchKandE();
        // $stations = BusStation::all();
        // foreach($stations as $station){
        //     if()
        // }
        foreach($dispatchData as $point){
            $station = BusStation::where('title', $point['name'])->first();
            if($station){
                if(isset($point['details'])){
                    $station->dispatch_point_id = $point['id'];
                    $station->kladr_id = null;
                }
                else{
                    $station->kladr_id = $point['id'];
                    $station->dispatch_point_id = null;
                }
                $station->save();
            }
        }
    }
}
