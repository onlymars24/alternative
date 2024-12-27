<?php

namespace Database\Seeders;

use DateTime;
use Exception;
use App\Models\Route;
use App\Models\Setting;
use App\Models\Station;
use App\Models\KladrsCouple;
use App\Models\DispatchPoint;
use App\Services\RaceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([
        ['dispatch_kladr_id', '=', 151370], 
          // ['dispatch_kladr_id', '=', 221627]
        ])
        ->take(70)
        ->get()
        ;
        $output = new ConsoleOutput();
        $totalItems = $kladrsCouples->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);
        $progressBar->start();

        $routes = [];
        $date = date('Y-m-d');

        
        foreach($kladrsCouples as $kladrsCouple){
          foreach(range(1, 7) as $i){
          $datetime = new DateTime($date);
          // $datetime->modify('+'.$i.' day');
          $datetime->modify('+'.$i.' day');
          $newDate = $datetime->format('Y-m-d');            
          $races = RaceService::optimizedGetByKladrs($kladrsCouple->dispatchKladr, $kladrsCouple->arrivalKladr, $newDate);
          // Log::info($kladrsCouple->dispatchKladr->name.' - '.$kladrsCouple->arrivalKladr->name.' рейсы: '.json_encode($races));
          if(gettype($races) == 'array' && count($races) > 0){
            foreach($races as $race){
              $route = Route::where([['num', '=', $race->num], ['name', '=', $race->name]])->first();
              if(!$route){
                $route = Route::create([
                  'num' => $race->num,
                  'name' => $race->name,
                  'minPrice' => $race->price,
                  'maxPrice' => $race->price,
                  'kladr_id' => $kladrsCouple->dispatchKladr->id
                ]);
                $route->kladrs_couples()->attach($kladrsCouple->id);
              }
              else{
                if($race->price < $route->minPrice){
                  $route->minPrice = $race->price;
                }
                if($race->price > $route->maxPrice){
                  $route->minPrice = $race->price;
                }
                $route->save();
              }
              // Log::info('$route->schedule '.gettype($route->schedule));
              $schedule = json_decode($route->schedule);
              // Log::info('$schedule '.gettype($schedule));
              $schedule = (array)$schedule;
              // Log::info('(array)$schedule '.gettype($schedule).' '.json_encode($schedule));
              $routeTime = date('H:i', strtotime($race->dispatchDate));
              if(!array_key_exists($routeTime, $schedule)){
                $schedule[$routeTime] = [];
              }
              else{
                $schedule[$routeTime] = (array)$schedule[$routeTime];
              }
              $schedule[$routeTime][date('w', strtotime($race->dispatchDate))] = true;

              $route->schedule = json_encode($schedule);
              $route->save();
              $routeKey = $race->num.' '.$race->name;
              // $routes[$routeKey]['schedule'][date('H:i', strtotime($race->dispatchDate))][(integer)date('w', strtotime($race->dispatchDate))] = true;

              // ЕСЛИ НЕТ СТОПОВ ТО ЗАПИСЫВАЕМ ИХ И ПРОВЕРЯЕМ ПЕРВЫЙ СТОП; ЕСЛИ ПЕРВЫЙ СТОП НЕ ПЕРВЫЙ ТО СТАВИМ КАСТОМНЫЙ; СОХРАНЯЕМ STATION_ID
              // В ОБОИХ СЛУЧАЯХ ИЩЕМ СТОП ДЛЯ ARRIVAL_STATION И ЗАПИСЫВАЕМ ТУДА STATION_ID
              
              // if(array_key_exists('stops', $routes[$routeKey])){
              //   $raceStops = $routes[$routeKey]['stops'];
              // }
              if(count((array)json_decode($route->stops)) > 0){
                $raceStops = (array)json_decode($route->stops);
              }
              else{
                try{
                  $raceSummary = Http::withHeaders([
                    'Authorization' => env('AVTO_SERVICE_KEY'),
                  ])->get(env('AVTO_SERVICE_URL').'/race/summary/'.$race->uid)->object();
                  $raceStops = isset($raceSummary->stops) ? $raceSummary->stops : [];
                }
                catch(Exception $e){
                    $raceStops = json_decode(json_encode(['errorMessage' => 'Поиск длился больше 10 секунд']));
                }

                sleep(1);
                if(gettype($raceStops) != 'array' || count($raceStops) == 0){
                  // Log::info($routeKey.': stops from http is empty ');
                  continue;
                }
                $raceStops = (array)$raceStops;
                $dispatchPoint = DispatchPoint::find($race->dispatchPointId);
                // if(!$dispatchPoint || !$dispatchPoint->station || !$dispatchPoint->station->kladr){
                //   Log::info($routeKey.': dispatchPoint is null ');
                //   continue;
                // }
                $dispatchStationId = null;
                if($dispatchPoint && $dispatchPoint->station){
                  $dispatchStationId = $dispatchPoint->station->id;
                }
                if($raceStops[0]->distance == 0){
                  $raceStops[0]->station_id = $dispatchStationId;
                  $raceStops[0]->kladr_id = $kladrsCouple->dispatchKladr->id;
                }
                else{
                  $newFirstStop = json_decode(json_encode(
                    [
                      "code"=> null,
                      "name" => $race->dispatchStationName,
                      "regionName"=> $kladrsCouple->dispatchKladr->region,
                      "arrivalDate"=> null,
                      "dispatchDate"=> null,
                      "stopTime"=> null,
                      "distance"=> 0,
                      "address" => null,
                      "station_id" => $dispatchStationId,
                      'kladr_id' => $kladrsCouple->dispatchKladr->id
                    ]
                  ));
                  array_unshift($raceStops, $newFirstStop);
                }
              }
              
              $route->station_id = $dispatchStationId;
              $route->save();

              $arrivalStation = Station::where([
                ['name', '=', $race->arrivalStationName],
                ['kladr_id', '=', $kladrsCouple->arrivalKladr->id],
              ])->first();
              // Log::info($routeKey.': arrivalStation for '.$race->arrivalStationName.' exists');
              foreach($raceStops as $stop){
                if($stop->name == $race->arrivalStationName){
                  $stop->station_id = $arrivalStation ? $arrivalStation->id : null;
                  $stop->kladr_id = $kladrsCouple->arrivalKladr->id;
                  break;
                }
              }

              // $routes[$routeKey]['stops'] = $raceStops;
              $route->lastCheckDate = date('Y-m-d H:i');
              $route->stops = json_encode($raceStops);
              $route->save();
            }
          }
          }
          $progressBar->advance();
        }
  
      $progressBar->finish();
      // dd($routes);
    }
}