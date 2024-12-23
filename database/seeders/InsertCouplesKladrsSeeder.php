<?php

namespace Database\Seeders;

use App\Models\PointsMatch;
use App\Models\KladrsCouple;
use App\Services\PointService;
use Illuminate\Database\Seeder;
use App\Models\KladrStationPage;
use Illuminate\Support\Facades\Log;
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
        $kladrPages = KladrStationPage::where([['kladr_id', '<>', null]])->get();
        foreach($kladrPages as $kladrPage){
            $dispatchKladr = $kladrPage->kladr;
            $arrivalKladrs = PointService::arrivalKladrsBySourceId($dispatchKladr->sourceId);
            foreach($arrivalKladrs as $arrivalKladr){
                if(!KladrsCouple::where([['dispatch_kladr_id', '=', $dispatchKladr->id], ['arrival_kladr_id', '=', $arrivalKladr->id]])->first()){
                    // KladrsCouple::create([
                    //    'dispatch_kladr_id' => $dispatchKladr->id,
                    //    'arrival_kladr_id' => $arrivalKladr->id,
                    // ]);
                    Log::info('автобус/'.$dispatchKladr->slug.'/'.$arrivalKladr->slug);
                }
            }
        }
    }
}