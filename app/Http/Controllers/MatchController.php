<?php

namespace App\Http\Controllers;

use App\Models\PointsMatch;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function all(){
        $pointsMatches = PointsMatch::all();
        return response([
            'pointsMatches' => $pointsMatches
        ]);
    }

    public function create(Request $request){
        // return response([
        //     'pointsMatch' => $request->all()
        // ]);
        $pointsMatch = PointsMatch::create([
            'orderPointId' => $request->orderPointId,
            'orderPointName' => $request->orderPointName,
            'matchPointId' => $request->matchPointId,
            'matchPointName' => $request->matchPointName,
            'dispatchPointId' => $request->dispatchPointId,
            'dispatchPointName' => $request->dispatchPointName,
            'pointType' => $request->pointType,
        ]);
        return response([
            'pointsMatch' => $pointsMatch
        ]);
    }

    public function delete(Request $request){
        $pointsMatch = PointsMatch::find($request->id);
        $pointsMatch->delete();
        return response([
            'status' => 200
        ]);
    }

    // dispatchPointName
    // arrivalPointName
    public function replacement(Request $request){
        $dispatchPointMatch = PointsMatch::where([['orderPointName', '=', $request->dispatchPointName], ['pointType', '=', 'Отправление']])->first();
        $dispatchPointName = null;

        if($dispatchPointMatch){
            $dispatchPointName = $dispatchPointMatch->matchPointName;
        }
        else{
            $dispatchPointName =  $request->dispatchPointName;
        }

        $arrivalPointMatch = PointsMatch::where([['orderPointId', '=', $request->arrivalPointName], ['dispatchPointName', '=', $dispatchPointName], ['pointType', '=', 'Прибытие']])->first();
        if(!$arrivalPointMatch){
            $arrivalPointMatch = PointsMatch::where([['orderPointId', '=', $request->arrivalPointName], ['pointType', '=', 'Прибытие']])->first();
        }

        return response([
            'newDispatchPointName' => $dispatchPointMatch ? $dispatchPointMatch->matchPointName : null,
            'newArrivalPointName' => $arrivalPointMatch ? $arrivalPointMatch->matchPointName : null,
        ]);
    }
}