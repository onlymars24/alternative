<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Models\CacheArrivalPoint;
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
        if(!Auth::user()->admin){
            return response([
                'errorMessage' => 'Ошибка доступа!'
            ], 401);
        }
        Log::info($request->dispatchPointId);
        $dispatchPoint = DispatchPoint::find($request->dispatchPointId);
        $dispatchPoint->kladr_id = $request->kladrId;
        $dispatchPoint->save();
        return response([
            'dispatchPoint' => $dispatchPoint
        ]);
        
    }

    public function addArrival(Request $request){
        if(!Auth::user()->admin){
            return response([
                'errorMessage' => 'Ошибка доступа!'
            ], 401);
        }
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
        $dispatchPoints = $kladr->dispatchPoints;
        $result = [];
        foreach($dispatchPoints as $dispatchPoint){
            $arrivalKladrs = Kladr::with('arrivalPoints.dispatchPoint')->whereHas('arrivalPoints', function(Builder $query) use ($dispatchPoint){
                $query->where([['dispatch_point_id', '=', $dispatchPoint->id]]);
            })->get()->toArray();     

            // $result = array_merge($result, $arrivalKladrs);    
            if(!$dispatchPoint->station->kladrStationPage->hidden){
                $result[] = [$dispatchPoint, $arrivalKladrs, $dispatchPoint->station->kladrStationPage];
            }   
            
        }

        return response(['arrivalKladrs' => $result]);
    }
}
