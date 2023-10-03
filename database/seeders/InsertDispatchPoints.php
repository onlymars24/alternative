<?php

namespace Database\Seeders;

use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertDispatchPoints extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = Http::withHeaders([
            'Authorization' => env('AVTO_SERVICE_KEY'),
        ])->get(env('AVTO_SERVICE_URL').'/regions/643')->object();
        $points = [];
        foreach($regions as $region){
            $pointsTemp = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
            ])->get(env('AVTO_SERVICE_URL').'/dispatch_points/'.$region->id)->object();
            if($pointsTemp){
                foreach($pointsTemp as $point){
                    DispatchPoint::create([
                        'id' => $point->id,
                        'name' => $point->name,
                        'region' => $point->region,
                        'details' => $point->details,
                        'address' => $point->address,
                        'latitude' => $point->latitude,
                        'longitude' => $point->longitude,
                        'okato' => $point->okato,
                        'place' => $point->place
                    ]);
                    // $points[] = $point;
                }   
            }
        }
        // dd($points); 
    }
}
