<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Models\CacheArrivalPoint;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertPointsIntoSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispatchPoints = DispatchPoint::all();
        $allPoints = [];
        foreach($dispatchPoints as $point){
          $allPoints[] = json_decode(json_encode($point->toArray()));
    
          $arrival_points = CacheArrivalPoint::where('dispatch_point_id', $point->id)->first();
    
          if(!$arrival_points){
              $arrival_points_remoted = Http::withHeaders([
                  'Authorization' => env('AVTO_SERVICE_KEY'),
              ])->get(env('AVTO_SERVICE_URL').'/arrival_points/'.$point->id)->object();
              $arrival_points = CacheArrivalPoint::create([
                  'dispatch_point_id' => $point->id,
                  'arrival_points' => json_encode($arrival_points_remoted)
              ]);
          }
          $allPoints = [...(array)json_decode($arrival_points->arrival_points), ...$allPoints];
        }
        Setting::create([
            'name' => 'allPoints',
            'data' => json_encode($allPoints)
        ]);
    }
}