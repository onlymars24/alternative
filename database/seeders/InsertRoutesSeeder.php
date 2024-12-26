<?php

namespace Database\Seeders;

use DateTime;
use Exception;
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
        // ['dispatch_kladr_id', '=', 151370], 
          ['dispatch_kladr_id', '=', 221627]
        ])
        ->take(100)
        ->get()
        ;
        $output = new ConsoleOutput();
        $totalItems = $kladrsCouples->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);
        $progressBar->start();

        $routes = [];
        $date = date('Y-m-d');
        $datetime = new DateTime($date);
        // $datetime->modify('+'.$i.' day');
        $datetime->modify('+1 day');
        $newDate = $datetime->format('Y-m-d');
        
        foreach($kladrsCouples as $kladrsCouple){
          $races = RaceService::optimizedGetByKladrs($kladrsCouple->dispatchKladr, $kladrsCouple->arrivalKladr, $newDate);
          // Log::info($kladrsCouple->dispatchKladr->name.' - '.$kladrsCouple->arrivalKladr->name.' рейсы: '.json_encode($races));
          $progressBar->advance();
          if(gettype($races) == 'array' && count($races) > 0){
            foreach($races as $race){
              $routeKey = $race->num.' '.$race->name;
              $routes[$routeKey]['schedule'][date('H:i', strtotime($race->dispatchDate))][] = (integer)date('w', strtotime($race->dispatchDate));
    
              // ЕСЛИ НЕТ СТОПОВ ТО ЗАПИСЫВАЕМ ИХ И ПРОВЕРЯЕМ ПЕРВЫЙ СТОП; ЕСЛИ ПЕРВЫЙ СТОП НЕ ПЕРВЫЙ ТО СТАВИМ КАСТОМНЫЙ; СОХРАНЯЕМ STATION_ID
              // В ОБОИХ СЛУЧАЯХ ИЩЕМ СТОП ДЛЯ ARRIVAL_STATION И ЗАПИСЫВАЕМ ТУДА STATION_ID
    
              if(array_key_exists('stops', $routes[$routeKey])){
                $raceStops = $routes[$routeKey]['stops'];
              }
              else{
                try{
                  $raceStops = Http::withHeaders([
                    'Authorization' => env('AVTO_SERVICE_KEY'),
                  ])->get(env('AVTO_SERVICE_URL').'/race/stops/'.$race->uid)->object();
                }
                catch(Exception $e){
                    $raceStops = json_decode(json_encode(['errorMessage' => 'Поиск длился больше 10 секунд']));
                }

                sleep(1);
                if(gettype($raceStops) != 'array' || count($raceStops) == 0){
                  Log::info($routeKey.': stops from http is empty ');
                  continue;
                }
                $raceStops = (array)$raceStops;
                $dispatchPoint = DispatchPoint::find($race->dispatchPointId);
                if(!$dispatchPoint || !$dispatchPoint->station || !$dispatchPoint->station->kladr){
                  Log::info($routeKey.': dispatchPoint is null ');
                  continue;
                }
                $dispatchStation = $dispatchPoint->station;
                $dispatchKladr = $dispatchPoint->station->kladr;
                if($raceStops[0]->distance == 0){
                  $raceStops[0]->station_id = $dispatchStation->id;
                }
                else{
                  $newFirstStop = json_decode(json_encode(
                    [
                      "code"=> null,
                      "name" => $dispatchStation->name,
                      "regionName"=> $dispatchKladr->region,
                      "arrivalDate"=> null,
                      "dispatchDate"=> null,
                      "stopTime"=> null,
                      "distance"=> 0,
                      "address" => null,
                      "station_id" => $dispatchStation->id,
                    ]
                  ));
                  array_unshift($raceStops, $newFirstStop);              
                }
              }

              $arrivalStation = Station::where([
                ['name', '=', $race->arrivalStationName],
                ['kladr_id', '=', $kladrsCouple->arrivalKladr->id],
              ])->first();
              if(!$arrivalStation || !$arrivalStation->kladr){
                Log::info($routeKey.': arrivalStation for '.$race->arrivalStationName.' is null ');
                $routes[$routeKey]['stops'] = $raceStops;
                continue;
              }
              Log::info($routeKey.': arrivalStation for '.$race->arrivalStationName.' exists');
              foreach($raceStops as $stop){
                if($stop->name == $race->arrivalStationName){
                  $stop->station_id = $arrivalStation->id;
                  break;
                }
              }
              $routes[$routeKey]['stops'] = $raceStops;
            }
          }
          
        }
    
      // }
      $setting = Setting::where('name', 'routesLogs')->first();
      if(!$setting){
        $setting = Setting::create([
            'name' => 'routesLogs',
            'data' => json_encode($routes)
        ]);
      }
      else{
        $setting->data = json_encode($routes);
        $setting->save();
      }
      $progressBar->finish();
      // dd($routes);

        
    }
}
