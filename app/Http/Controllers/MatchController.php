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

    public function replacement(Request $request){
        $dispatchPointMatch = PointsMatch::where([['orderPointId', '=', $request->dispatchPointId], ['pointType', '=', 'Отправление']])->first();
        $arrivalPointMatch = PointsMatch::where([['orderPointId', '=', $request->arrivalPointId], ['pointType', '=', 'Прибытие']])->first();
        return response([
            'newDispatchPointId' => $dispatchPointMatch ? $dispatchPointMatch->matchPointId : null,
            'newArrivalPointId' => $arrivalPointMatch ? $arrivalPointMatch->matchPointId : null,
        ]);
    }
}