<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\PointService;
use App\Models\CacheArrivalPoint;
use App\Models\KladrsCouple;
use App\Services\SlugService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class KladrController extends Controller
{
    public function all(Request $request){
        return response([
            'kladrs' => Kladr::where('name', 'like', '%'.$request->kladrFilter.'%')->get()
        ]);
    }

    public function addDispatch(Request $request){
        // if(!Auth::user()->admin){
        //     return response([
        //         'errorMessage' => 'Ошибка доступа!'
        //     ], 401);
        // }
        Log::info($request->dispatchPointId);
        $dispatchPoint = DispatchPoint::find($request->dispatchPointId);
        $dispatchPoint->kladr_id = $request->kladrId;
        $dispatchPoint->save();
        if($request->kladrId){
            $kladr = Kladr::find($request->kladrId);
            $kladr->slug = SlugService::create($kladr->name);
            $kladr->sourceId = 'kladrs-'.$kladr->id;
            $kladr->save();            
        }
        return response([
            'dispatchPoint' => $dispatchPoint
        ]);
        
    }

    public function addArrival(Request $request){
        // if(!Auth::user()->admin){
        //     return response([
        //         'errorMessage' => 'Ошибка доступа!'
        //     ], 401);
        // }
        // return response([
        //     'arrivalPoint' => $request->all()
        // ]);
        $arrivalPoint = CacheArrivalPoint::where([
        ['dispatch_point_id', '=', $request->dispatchPointId], 
        ['arrival_point_id', '=', $request->arrivalPointId]])->first();
        // return response([
        //     'arrivalPoint' => $arrivalPoint
        // ]);
        $arrivalPoint->kladr_id = $request->kladrId;
        $arrivalPoint->save();
        if($request->kladrId){
            $kladr = Kladr::find($request->kladrId);
            $kladr->slug = SlugService::create($kladr->name);
            $kladr->sourceId = 'kladrs-'.$kladr->id;
            $kladr->save();            
        }
        return response([
            'arrivalPoint' => $arrivalPoint
        ]);
    }

    public function allConnected(Request $request){
        return response([
            'kladrs' => Kladr::has('dispatchPoints')->orHas('arrivalPoints')->with('kladrStationPage')->where('name', 'like', '%'.$request->kladrFilter.'%')->get()
        ]);
    }

    public function races(Request $request){
        $kladr = Kladr::find($request->kladrId);
        $stations = $kladr->stations;
        $result = [];
        foreach($stations as $station){
            if($station->kladrStationPage && !$station->kladrStationPage->hidden){
                $result[] = [$station, PointService::arrivalDataBySourceId($station->sourceId), $station->kladrStationPage];
            }   
            
        }

        return response(['arrivalKladrs' => $result]);
    }

    public function links(Request $request){
        $links = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where('dispatch_kladr_id', $request->kladrId)
        ->orderBy(Kladr::select('name')->whereColumn('kladrs.id', 'kladrs_couples.arrival_kladr_id'), 'asc')->get();
        $linksData = [];
        foreach($links as $link){
            // return response(['$link->arrivalKladr->name' => $link->arrivalKladr->name]);
            $key = mb_substr($link->arrivalKladr->name, 0, 1);
            if(preg_match('/[а-яА-ЯЁё]/u', $key)){
                $linksData[$key][] = $link;
            }
            
            // return response(['$link->arrivalKladr->name' => $linksData]);
        }
        ksort($linksData);
        // foreach($linksData as $linkCharacter){
            
        //     usort($linkCharacter, function($a, $b) {
        //         return strcmp($a->arrivalKladr->name, $b->arrivalKladr->name);
        //     });
        // }
        return response(['links' => $linksData]);
    }
}
