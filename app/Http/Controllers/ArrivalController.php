<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CacheArrivalPoint;

class ArrivalController extends Controller
{
    public function paginate(Request $request){
        $whereArr = [];                
        if($request->pointNameSearch){
            $whereArr[] = ['name', 'like', '%'.$request->pointNameSearch.'%'];
        }
        if($request->kladrId){
            $whereArr[] = ['kladr_id', '=', $request->kladrId];
        }
        if($request->dispatchPointId){
            $whereArr[] = ['dispatch_point_id', '=', $request->dispatchPointId];
        }
        if($request->noKladr){
            $whereArr[] = ['kladr_id', '=', null];
        }
        return response([
            'points' => CacheArrivalPoint::with('dispatchPoint', 'kladr')->where($whereArr)->paginate(13)
        ]);
    }
}
