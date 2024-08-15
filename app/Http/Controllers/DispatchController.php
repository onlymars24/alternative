<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\PointService;
use Illuminate\Database\Eloquent\Builder;

class DispatchController extends Controller
{
    public function paginate(Request $request){

        $whereArr = [];                
        if($request->pointNameSearch){
            $whereArr[] = ['name', 'like', '%'.$request->pointNameSearch.'%'];
        }
        if($request->kladrId){
            $whereArr[] = ['kladr_id', '=', $request->kladrId];
        }
        if($request->noKladr){
            $whereArr[] = ['kladr_id', '=', null];
        }

        return response([
            'points' => DispatchPoint::with('kladr')->where($whereArr)->paginate(13)
        ]);
    }

    public function all(){
        return response([
            'dispatchPoints' => PointService::dispatchKandE(),
        ]);
    }
}