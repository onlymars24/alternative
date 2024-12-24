<?php

namespace Database\Seeders;

use Nette\Utils\DateTime;
use App\Models\KladrsCouple;
use Illuminate\Database\Seeder;
use App\Services\VkMarketService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertVkMarketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([['racesExistence', '=', null]])->get();
        $output = new ConsoleOutput();
        $totalItems = $kladrsCouples->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);
        $progressBar->start();
        // $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([['dispatch_kladr_id', '=', 169902], ['arrival_kladr_id', '=', 206690]])->get();
        // dd($kladrsCouples);
        $date = date("Y-m-d");
        $newCouples = [];
        $newRaces = [];
        foreach($kladrsCouples as $couple){
          $dispatchKladr = $couple->dispatchKladr;
          $arrivalKladr = $couple->arrivalKladr;
          $dispatchPoint = $dispatchKladr->dispatchPoints->where('name', $dispatchKladr->name)->first();
          $dispatchPoints = $dispatchPoint ? [$dispatchPoint] : $dispatchKladr->dispatchPoints;
  
          $arrivalPoint = $arrivalKladr->arrivalPoints->where('name', $arrivalKladr->name)->first();
          $arrivalPoints = $arrivalPoint ? [$arrivalPoint] : $arrivalKladr->arrivalPoints;
          
          
          
          // $races = [];
          $key = '/автобус/'.$dispatchKladr->slug.'/'.$arrivalKladr->slug;
          if($dispatchPoint && $arrivalPoint){
          // $newCouples[$key] = [$dispatchPoints, $arrivalPoints];
            $progressBar->advance();
            continue;
          }
          $newCouples[$key] = [$dispatchPoints, $arrivalPoints];
          
          
          $races = [];
          for($i = 1; $i <= 7; $i++){
            $datetime = new DateTime($date);
            $datetime->modify('+'.$i.' day');
            $newDate = $datetime->format('Y-m-d');
            foreach($dispatchPoints as $dispatchPoint){
              foreach($arrivalPoints as $arrivalPoint){
                Log::info('first request '.$key.' '.env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$newDate);
                $races = Http::withHeaders([
                  'Authorization' => env('AVTO_SERVICE_KEY'),
                ])->get(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$newDate)->object();
                sleep(2);
  
                if(gettype($races) == 'array' && count($races) > 0){
                  Log::info($key.' есть рейсы '.json_encode($races));
                  break;
                }
                elseif(gettype($races) == 'object'){
                  Log::info($key.' ошибка '.json_encode($races));
                  break;
                }
                Log::info($key.' '.json_encode($races));
              }
              if(gettype($races) == 'array' && count($races) > 0){
                break;
              }
            }
  
            if(gettype($races) == 'array' && count($races) > 0){
              break;
            }
            elseif(gettype($races) == 'object'){
              break;
            }
          }
          if(gettype($races) == 'array' && count($races) > 0){
            $minPrice = $races[0]->price;
            foreach($races as $race){
              if($race->price < $minPrice){
                $minPrice = $race->price;
              }
            }
            VkMarketService::marketAdd($dispatchKladr, $arrivalKladr, $minPrice);
            $couple->racesExistence = true;
            $newRaces[] = ['races' => $races, 'minPrice' => $minPrice];
          }
          else{
            $couple->racesExistence = false;
          }
          $couple->save();
          $progressBar->advance();
        }

        
    // }
    $progressBar->finish();
    $output->writeln('Seeder completed!');
    }
}
