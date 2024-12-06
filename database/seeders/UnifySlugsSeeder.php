<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\Station;
use App\Services\SlugService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnifySlugsSeeder extends Seeder
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
            SlugService::unifyStationSlug($station);
        }
        SlugService::unifyArrivalPointsSlugs();
        SlugService::unifyKladrsSlugs();

        // $kladrs = Kladr::has('dispatchPoints')->orHas('arrivalPoints')->get();
        // foreach($kladrs as $kladr){
        //     $kladr->slug = SlugService::create($kladr->name);
        //     $kladr->save();
        // }
    }
}
