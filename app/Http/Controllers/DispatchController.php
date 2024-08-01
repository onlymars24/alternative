<?php

namespace App\Http\Controllers;

use App\Models\DispatchPoint;
use Illuminate\Http\Request;

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
            'points' => DispatchPoint::with('kladr')->where($whereArr)->paginate(5)
        ]);
    }
}