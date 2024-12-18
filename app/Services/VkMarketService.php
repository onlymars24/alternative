<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VkMarketService
{
    public static function marketAdd($dispatchName, $arrivalName){
        GraphicService::generateImage($dispatchName, $arrivalName);

        $response =  Http::get(env('VK_URL').'/market.getProductPhotoUploadServer?v=5.131&group_id='
        .env('VK_GROUP_ID').'&access_token='.env('VK_TOKEN'))->object();
        if(!isset($response->response->upload_url)){
            return 'error market.getProductPhotoUploadServer';
        }
        $upload_url = $response->response->upload_url;

        $upload_response =  Http::attach('file', file_get_contents(public_path('/routes/new_bus_bgc.jpg')), 'new_bus_bgc.jpg')->post($upload_url)->object();

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
        
        if(!isset($response->response->photo_id)){
            return $response;
        }

        $response =  Http::get(env('VK_URL').'/market.add?
        owner_id='.!env('VK_GROUP_ID').'
        &v=5.131
        &category_id=30282
        &name=Автобус Томск - Екатеринбург
        &description=Автобус Томск - Екатеринбург отправлением с автовокзала Томск
        &main_photo_id=457239057
        &price=1900
        &url=https://xn--80adplhnbnk0i.xn--p1ai/автобус/'.$dispatchName.'/'.$arrivalName.'
        &access_token='.env('VK_TOKEN'))->object();        
        // $response =  Http::post(env('VK_URL').'/market.saveProductPhoto?v=5.131&group_id='
        // .env('VK_GROUP_ID').'&access_token='.env('VK_TOKEN'), ['upload_response' => $upload_response])->object();

        // $response =  Http::post(env('VK_URL').'/market.saveProductPhoto', [
        //     'v' => '5.131', 
        //     'group_id' => env('VK_GROUP_ID'), 
        //     'access_token' => env('VK_TOKEN'), 
        //     'upload_response' => $upload_response
        // ])->object();

        return $response;
    }
}