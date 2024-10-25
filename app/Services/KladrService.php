<?php

namespace App\Services;

use App\Models\Kladr;
use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class KladrService
{    
    // dispatch 
    // arrival
    public static function connectPointIntoKladr($point){
        if(!$point->region){
            return null;
        }
        $whereArr = [
            ['name', '=', $point->name],
            ['region', '=', $point->region],
            ['code', 'like', '%00']
        ];

        if(str_contains($point->details, 'р-н')){
            $whereArr[] = ['district', '=', $point->details];
        }

        $kladrs = Kladr::where($whereArr)->get();
        if($kladrs->count() == 1){
            return $kladrs[0]->id;
        }
        else{
            $query = "INSTR('$point->name', name) > 0 AND INSTR('$point->region', region) > 0";
            if(str_contains($point->details, 'р-н')){
                $query = $query." AND INSTR('$point->details', district) > 0";
            }
            $kladrs = Kladr::whereRaw($query)->get();
            if($kladrs->count() == 1){
                return $kladrs[0]->id;
            }
        }
        return null;
    }
    
    public static function decode($kladr){
        $code = $kladr->code;
        $codes = [
            'region' => mb_strcut($code, 0, 2),
            'district' => mb_strcut($code, 2, 3),
            'city' => mb_strcut($code, 5, 3),
            'settlement' => mb_strcut($code, 8, 3),
            'relevance' => mb_strcut($code, 11, 2),
        ];
        if($codes['district'] == '000' && $codes['city'] == '000' && $codes['settlement'] == '000'){
            return;
        }

        $kladrRegion = Kladr::where('code', $codes['region'].'00000000000')->first();
        if($kladrRegion){
            $kladr->region = $kladrRegion->name.' '.$kladrRegion->socr;
        }
        
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


        $relevance = mb_strcut($kladr->code, 11, 2);
        if($relevance == '00'){
            $kladr->relevance = 'Актуальный объект';
        }
        elseif($relevance >= '01' && $relevance <= '50'){
            $kladr->relevance = 'Объект был переименован';
        }
        elseif($relevance == '51'){
            $kladr->relevance = 'Объект был переподчинён';
        }
        elseif($relevance >= '52' && $relevance <= '98'){
            $kladr->relevance = 'Резервное значение признака актуальности';
        }
        elseif($relevance == '99'){
            $kladr->relevance = 'Адресный объект не существует';
        }
        $kladr->save();
    }
}