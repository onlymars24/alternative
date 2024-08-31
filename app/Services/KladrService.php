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
}