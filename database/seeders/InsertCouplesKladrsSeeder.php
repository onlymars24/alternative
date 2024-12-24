<?php

namespace Database\Seeders;

use App\Models\PointsMatch;
use App\Models\KladrsCouple;
use App\Services\PointService;
use Illuminate\Database\Seeder;
use App\Models\KladrStationPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertCouplesKladrsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $kladrPages = KladrStationPage::where([['kladr_id', '=', 221627]])->get();
        $kladrPages = KladrStationPage::where([['kladr_id', '<>', null]])->get();
        $couplesData = [];
        foreach($kladrPages as $kladrPage){
            $dispatchKladr = $kladrPage->kladr;
            $arrivalKladrs = PointService::arrivalKladrsBySourceId($dispatchKladr->sourceId);
            foreach($arrivalKladrs as $arrivalKladr){
                if(!KladrsCouple::where([['dispatch_kladr_id', '=', $dispatchKladr->id], ['arrival_kladr_id', '=', $arrivalKladr->id]])->first()){
                    KladrsCouple::create([
                       'dispatch_kladr_id' => $dispatchKladr->id,
                       'arrival_kladr_id' => $arrivalKladr->id,
                    ]);
                }
            }

            // if($dispatchKladr->album_id){
            //     continue;
            // }
            // $title = 'Автовокзалы и автостанции '.$dispatchKladr->name;
            // $response = Http::get(env('VK_URL').'/market.addAlbum?owner_id='.-env('VK_GROUP_ID').'
            // &v=5.131
            // &group_id='
            // .env('VK_GROUP_ID').'&access_token='.env('VK_TOKEN').'&title='.$title)->object();
            // if(isset($response->response->market_album_id)){
            //     $dispatchKladr->album_id = $response->response->market_album_id;
            //     $dispatchKladr->save();
            // }
        }
    }
}