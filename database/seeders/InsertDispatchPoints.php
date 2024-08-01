<?php

namespace Database\Seeders;

use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Models\CacheArrivalPoint;
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
        $dispatchPoints = DispatchPoint::all();
        foreach($dispatchPoints as $dispatchPoint){
        //   $dispatchPoint = DispatchPoint::create([
        //       'id' => $point->id,
        //       'name' => $point->name,
        //       'region' => $point->region,
        //       'details' => $point->details,
        //       'address' => $point->address,
        //       'latitude' => $point->latitude,
        //       'longitude' => $point->longitude,
        //       'okato' => $point->okato,
        //       'place' => $point->place
        //   ]);
          $arrival_points_remoted = Http::withHeaders([
              'Authorization' => env('AVTO_SERVICE_KEY'),
          ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$dispatchPoint->id)->object();
          foreach($arrival_points_remoted as $point){
              $arrival_point = CacheArrivalPoint::create([
                  'arrival_point_id' => $point->id,
                  'name' => $point->name,
                  'region' => $point->region,
                  'details' => $point->details,
                  'address' => $point->address,
                  'latitude' => $point->latitude,
                  'longitude' => $point->longitude,
                  'okato' => $point->okato ? $point->okato : '1',
                  'place' => $point->place ? $point->place : 1,
                  'dispatch_point_id' => $dispatchPoint->id,
              ]);                
          }
          // $points[] = $point;
      }  
        // $regions = Http::withHeaders([
        //     'Authorization' => env('AVTO_SERVICE_KEY'),
        // ])->get(env('AVTO_SERVICE_URL').'/regions/643')->object();
        // $points = [];
        // foreach($regions as $region){
        //     $pointsTemp = Http::withHeaders([
        //         'Authorization' => env('AVTO_SERVICE_KEY'),
        //     ])->get(env('AVTO_SERVICE_URL').'/dispatch_points/'.$region->id)->object();
        //     if($pointsTemp){
        //         foreach($pointsTemp as $point){
        //             $dispatchPoint = DispatchPoint::create([
        //                 'id' => $point->id,
        //                 'name' => $point->name,
        //                 'region' => $point->region,
        //                 'details' => $point->details,
        //                 'address' => $point->address,
        //                 'latitude' => $point->latitude,
        //                 'longitude' => $point->longitude,
        //                 'okato' => $point->okato,
        //                 'place' => $point->place
        //             ]);
        //             $arrival_points_remoted = Http::withHeaders([
        //                 'Authorization' => env('AVTO_SERVICE_KEY'),
        //             ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$point->id)->object();
        //             foreach($arrival_points_remoted as $point){
        //                 $arrival_points = CacheArrivalPoint::create([
        //                     'arrival_point_id' => $point->id,
        //                     'name' => $point->name,
        //                     'region' => $point->region,
        //                     'details' => $point->details,
        //                     'address' => $point->address,
        //                     'latitude' => $point->latitude,
        //                     'longitude' => $point->longitude,
        //                     'okato' => $point->okato ? $point->okato : '1',
        //                     'place' => $point->place ? $point->place : 1,
        //                     'dispatch_point_id' => $dispatchPoint->id,
        //                 ]);                
        //             }
        //             // $points[] = $point;
        //         }   
        //     }
        // }
        // dd($points); 
    }
}
