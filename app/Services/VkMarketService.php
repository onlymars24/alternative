<?php

namespace App\Services;

use App\Models\Kladr;
use App\Models\Order;
use App\Enums\FermaEnum;
use Nette\Utils\DateTime;
use App\Models\KladrsCouple;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Exception;

class VkMarketService
{

    // name
    // slug
    // price
    public static function marketAdd($dispatchKladr, $arrivalKladr){
        GraphicService::generateImage($dispatchKladr->name, $arrivalKladr->name);

        $response =  Http::get(env('VK_URL').'/market.getProductPhotoUploadServer?v=5.131&group_id='
        .env('VK_GROUP_ID').'&access_token='.env('VK_TOKEN'))->object();
        sleep(1);
        if(!isset($response->response->upload_url)){
            return 'error market.getProductPhotoUploadServer';
        }
        $upload_url = $response->response->upload_url;

        $upload_response =  Http::attach('file', file_get_contents(public_path('/routes/new_bus_bgc.jpg')), 'new_bus_bgc.jpg')->post($upload_url)->object();
        sleep(1);
        if(!isset($upload_response->sha)){
            return 'error '.$upload_url;
        }
        // return $upload_response;
        $curl = curl_init(); // Инициализируем запрос
        curl_setopt_array($curl, array(
            // CURLOPT_URL => route('order.confirm', ['order_id' => $order->id]), // Полный адрес метода
            CURLOPT_URL => env('VK_URL').'/market.saveProductPhoto', 
            CURLOPT_RETURNTRANSFER => true, // Возвращать ответ
            CURLOPT_POST => true, // Метод POST
            CURLOPT_POSTFIELDS => http_build_query([
                'v' => '5.131', 
                'group_id' => env('VK_GROUP_ID'), 
                'access_token' => env('VK_TOKEN'), 
                'upload_response' => json_encode($upload_response)
            ]) // Данные в запросе
        ));
        $response = curl_exec($curl); // Выполняем запрос
        $response = json_decode($response);
        curl_close($curl); // Закрываем соединение
        sleep(1);
        if(!isset($response->response->photo_id)){
            return $response;
        }

        $responseMarket =  Http::get(env('VK_URL').'/market.add?owner_id='.-env('VK_GROUP_ID').'
        &v=5.131
        &category_id=30282
        &name=Автобус '.$dispatchKladr->name.' — '.$arrivalKladr->name.'
        &description=Автобус '.$dispatchKladr->name.' — '.$arrivalKladr->name.': расписание, отправление и прибытие по местному времени, цена билетов, маршрут.
        &main_photo_id='.$response->response->photo_id.'
        &url=https://xn--80adplhnbnk0i.xn--p1ai/автобус/'.$dispatchKladr->slug.'/'.$arrivalKladr->slug.'?utm_source=vk&utm_medium=market
        &access_token='.env('VK_TOKEN'))->object();
        sleep(1);

        $marketId = isset($responseMarket->response->market_item_id) ? $responseMarket->response->market_item_id : null;
        if($dispatchKladr->album_id && $marketId){
          $response = Http::get(env('VK_URL').'/market.addToAlbum?owner_id='.-env('VK_GROUP_ID').'
          &v=5.131
          &item_ids='.$marketId.'
          &album_ids='.$dispatchKladr->album_id.
          '&access_token='.env('VK_TOKEN'))->object();
        }

        return $marketId;
    }

    public static function allMarketsAdd(){
      ini_set('max_execution_time', 20000);

      $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([['racesExistence', '=', null]])->take(10)->get();
      // $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([['dispatch_kladr_id', '=', 169902], ['arrival_kladr_id', '=', 206690]])->get();
      // dd($kladrsCouples);
      // $date = date("Y-m-d");
      // $newCouples = [];
      // $newRaces = [];
      foreach($kladrsCouples as $couple){
        $dispatchKladr = $couple->dispatchKladr;
        $arrivalKladr = $couple->arrivalKladr;

        if($dispatchKladr && $arrivalKladr){
          // $couple->racesExistence = true;
          $couple->market_id = self::marketAdd($dispatchKladr, $arrivalKladr);
          $couple->save();
        }

        $dispatchPoint = $dispatchKladr->dispatchPoints->where('name', $dispatchKladr->name)->first();
        $dispatchPoints = $dispatchPoint ? [$dispatchPoint] : $dispatchKladr->dispatchPoints;

        $arrivalPoint = $arrivalKladr->arrivalPoints->where('name', $arrivalKladr->name)->first();
        $arrivalPoints = $arrivalPoint ? [$arrivalPoint] : $arrivalKladr->arrivalPoints;
        
        
        
        // $races = [];
        $key = '/автобус/'.$dispatchKladr->slug.'/'.$arrivalKladr->slug;
        // if($dispatchPoint && $arrivalPoint){
        // // $newCouples[$key] = [$dispatchPoints, $arrivalPoints];
        //   continue;
        // }
        // $newCouples[$key] = [$dispatchPoints, $arrivalPoints];
        
        
        // $races = [];
        // for($i = 1; $i <= 7; $i++){
        //   $datetime = new DateTime($date);
        //   $datetime->modify('+'.$i.' day');
        //   $newDate = $datetime->format('Y-m-d');
        //   foreach($dispatchPoints as $dispatchPoint){
        //     foreach($arrivalPoints as $arrivalPoint){
        //       Log::info('first request '.$key.' '.env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$newDate);
        //       try{
        //         $races = Http::withHeaders([
        //           'Authorization' => env('AVTO_SERVICE_KEY'),
        //         ])->get(env('AVTO_SERVICE_URL').'/races/'.$dispatchPoint->id.'/'.$arrivalPoint->arrival_point_id.'/'.$newDate)->object();
        //       }
        //       catch(Exception $e){
        //         $races = [];
        //       }
              
        //       sleep(2);

        //       if(gettype($races) == 'array' && count($races) > 0){
        //         Log::info($key.' есть рейсы '.json_encode($races));
        //         break;
        //       }
        //       elseif(gettype($races) == 'object'){
        //         Log::info($key.' ошибка '.json_encode($races));
        //         break;
        //       }
        //       Log::info($key.' '.json_encode($races));
        //     }
        //     if(gettype($races) == 'array' && count($races) > 0){
        //       break;
        //     }
        //   }

        //   if(gettype($races) == 'array' && count($races) > 0){
        //     break;
        //   }
        //   elseif(gettype($races) == 'object'){
        //     break;
        //   }
        // }
        // if(gettype($races) == 'array' && count($races) > 0){
        //   $minPrice = $races[0]->price;
        //   foreach($races as $race){
        //     if($race->price < $minPrice){
        //       $minPrice = $race->price;
        //     }
        //   }
        //   // $couple->racesExistence = true;
        //   // $couple->market_id = self::marketAdd($dispatchKladr, $arrivalKladr, $minPrice);
        //   // $couple->save();
        //   $newRaces[] = ['races' => $races, 'minPrice' => $minPrice];


        // }
        // else{
        //   $couple->racesExistence = false;
        // }
        // $couple->save();
      }
      // return $newRaces;
      // dd($newCouples, $newRaces);
    }

    public static function allMarketsAddOld(){
        ini_set('max_execution_time', 20000);
        $date = date("Y-m-d");
        // dd(file_get_contents(public_path('/routes/new_bus_bgc.jpg')));
        // dd(gettype(-env('VK_GROUP_ID')), -env('VK_GROUP_ID'));
        // dd(VkMarketService::marketAdd('Москва', 'Воронеж'));
        $dispatchKladrs = Kladr::has('dispatchPoints')->get();
        // $count = 0;
        $pointsCouples = [];
      
        foreach($dispatchKladrs as $dispatchKladr){
          // $sourceId = explode('-', $kladr->sourceId);
          $stations = [];
          
          // $kladr = Kladr::find($sourceId[1]);
          $stations = $dispatchKladr->stations;
          foreach($stations as $station){
            if(!$station->dispatchPoints){
                continue;
            }
            foreach($station->dispatchPoints as $dispatchPoint){
                $arrivalKladrs = Kladr::whereHas('arrivalPoints', function(Builder $query) use ($dispatchPoint){
                    $query->where([['dispatch_point_id', '=', $dispatchPoint->id]]);
                })->get();
                foreach($arrivalKladrs as $arrivalKladr){
                    if($arrivalKladr->arrivalPoints->where('name', $arrivalKladr->name)->first()){
                      foreach($arrivalKladr->arrivalPoints as $arrivalPoint){
                        if($arrivalPoint->name == $arrivalKladr->name && $dispatchPoint->name == $dispatchKladr->name){
                          $pointsCouples[$dispatchKladr->name.' - '.$arrivalKladr->name] = [$dispatchPoint, [$arrivalPoint], $dispatchKladr, $arrivalKladr];
                        }
                      }
                    }
                    elseif($dispatchPoint->name == $dispatchKladr->name){
                      $pointsCouples[$dispatchKladr->name.' - '.$arrivalKladr->name] = [$dispatchPoint, $arrivalKladr->arrivalPoints->where('dispatch_point_id', $dispatchPoint->id), $dispatchKladr, $arrivalKladr];
                    }
                }
            }
          }
          // $count += count($races);
        }
        $pointsCouples = array_slice($pointsCouples, 25, 25);
        Log::info('$pointsCouples '.json_encode($pointsCouples));
        $newRaces = [];
        
        foreach($pointsCouples as $key => $couple){
          $races = [];
          for($i = 1; $i <= 7; $i++){
            $datetime = new DateTime($date);
            $datetime->modify('+'.$i.' day');
            $newDate = $datetime->format('Y-m-d');
            foreach($couple[1] as $arrivalPoint){
              Log::info('first request '.env('AVTO_SERVICE_URL').'/races/'.$couple[0]->id.'/'.$arrivalPoint->arrival_point_id.'/'.$newDate);
              $races = Http::withHeaders([
                'Authorization' => env('AVTO_SERVICE_KEY'),
              ])->get(env('AVTO_SERVICE_URL').'/races/'.$couple[0]->id.'/'.$arrivalPoint->arrival_point_id.'/'.$newDate)->object();
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
            self::marketAdd($couple[2], $couple[3], $minPrice); 
            $newRaces[] = ['races' => $races, 'minPrice' => $minPrice];
          }
        }
        return $newRaces;
    }
}