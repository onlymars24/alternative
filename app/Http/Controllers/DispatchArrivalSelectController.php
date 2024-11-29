<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use App\Models\Station;
use Illuminate\Http\Request;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;

class DispatchArrivalSelectController extends Controller
{
    public function selectDispatch(Request $request){
        return response([
            'dispatchData' => PointService::dispatchData($request->search)
        ]);
    }

    public function selectArrival(Request $request){
        return response(['arrivalData' => PointService::arrivalDataBySourceId($request->sourceId, $request->search)]);
    }

    public function checkUrlParams(Request $request){
        // $selectData = [
        //     'dispatchItem' => null,
        //     'arrivalItem' => null
        // ]

        $dispatchItem = Kladr::with('kladrStationPage', 'stations.dispatchPoints')->has('dispatchPoints')->where([['slug', '=', $request->dispatchSlug]])->first();

        if(!$dispatchItem){
            $dispatchItem = Station::with('kladr.kladrStationPage', 'dispatchPoints')->has('dispatchPoints')->where([['slug', '=', $request->dispatchSlug]])->first();
        }        
        $arrivalItem = null;
        if($dispatchItem){
            $arrivalData = PointService::arrivalDataBySourceId($dispatchItem->sourceId);
            $arrivalItem = array_filter($arrivalData, function($item) use($request) {
                return $item->slug == $request->arrivalSlug 
                && stripos($item->sourceId, 'kladrs') !== false // Четные числа
                ;
            });
            if(!$arrivalItem){
                $arrivalItem = array_filter($arrivalData, function($item) use($request) {
                    return $item->slug == $request->arrivalSlug 
                    && stripos($item->sourceId, 'stations') !== false // Четные числа
                    ;
                });
            }
            if(!$arrivalItem){
                $arrivalItem = array_filter($arrivalData, function($item) use($request) {
                    return $item->slug == $request->arrivalSlug 
                    && stripos($item->sourceId, 'cache_arrival_points') !== false // Четные числа
                    ;
                });
            }
            if($arrivalItem){
                $arrivalItem = $arrivalItem[array_key_first($arrivalItem)];
            }
            else{
                $arrivalItem = null;
            }
        }

        return response([
            'dispatchItem' => $dispatchItem,
            'arrivalItem' => $arrivalItem,
        ]);
    }
}