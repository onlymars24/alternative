<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;
use App\Models\CacheArrivalPoint;
use App\Services\SlugService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConnectStationsSeeder extends Seeder
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
            $station->slug = SlugService::create($station->name);
            $station->save();
        }
        $arrivalPoints = CacheArrivalPoint::all();
        foreach($arrivalPoints as $arrivalPoint){
            if(!$arrivalPoint->kladr){
                continue;
            }
            $kladr = $arrivalPoint->kladr;
            $station = Station::where([['name', '=', $arrivalPoint->name], ['kladr_id', '=', $kladr->id]])->first();
            if(!$station){
                $station = Station::create([
                    'name' => $arrivalPoint->name,
                    'slug' => SlugService::create($arrivalPoint->name),
                    'kladr_id' => $kladr->id
                ]);
                $station->sourceId = 'stations_'.$station->id;
                $station->save();
            }
            $arrivalPoint->station_id = $station->id;
            $arrivalPoint->save();
        }
    }
}