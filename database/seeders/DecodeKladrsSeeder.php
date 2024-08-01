<?php

namespace Database\Seeders;

use App\Models\Kladr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DecodeKladrsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kladrs = Kladr::where([['id', '>', 230803], ['code', 'like', '%00']])->take(200000)->get();
        $regions = [];
        foreach($kladrs as $kladr){
            $code = $kladr->code;
            $codes = [
                'region' => mb_strcut($code, 0, 2),
                'district' => mb_strcut($code, 2, 3),
                'city' => mb_strcut($code, 5, 3),
                'settlement' => mb_strcut($code, 8, 3),
                'relevance' => mb_strcut($code, 11, 2),
            ];
            if($codes['district'] == '000' && $codes['city'] == '000' && $codes['settlement'] == '000'){
                continue;
            }
            if(!isset($regions[$codes['region']])){
                $kladrRegion = Kladr::where('code', $codes['region'].'00000000000')->first();
                if($kladrRegion){
                    $regions[$codes['region']] = $kladrRegion->name.' '.$kladrRegion->socr;
                }
            }

            $kladr->region = $regions[$codes['region']];
            
            if($codes['district'] != '000'){
                $klardDistrict = Kladr::where('code', $codes['region'].$codes['district'].'00000000')->first();
                if($klardDistrict){
                    $kladr->district = $klardDistrict->name.' '.$klardDistrict->socr;
                }
            }

            if($codes['city'] != '000' && $codes['settlement'] != '000'){
                $klardCity = Kladr::where('code', $codes['region'].'000'.$codes['city'].'00000')->first();
                if($klardCity){
                    $kladr->city = $klardCity->name;
                }
            }





            $kladr->save();
            
            // Log::info($kladr->code, $codes);
        }
    }
}
