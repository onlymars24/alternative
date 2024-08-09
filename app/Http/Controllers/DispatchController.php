<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
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
        // $dispatchPoints1 = DispatchPoint::doesntHave('kladr')->get();
        // $dispatchPoints2 = DispatchPoint::whereHas('kladr', function(Builder $query){
        //     $query->has('dispatchPoints', '>', 1);
        //   })
        //   ->with('kladr.dispatchPoints')
        //   ->get();  
        $dispatchPoints = DispatchPoint::with('kladr.arrivalPoints')->get()->toArray();
        $kladrs = Kladr::has('dispatchPoints')->get()->toArray();
        $result = array_reduce($dispatchPoints, function ($carry, $item) {
            if (!in_array($item['name'], array_column($carry, 'name'))
                || !in_array($item['region'], array_column($carry, 'region'))
                || !in_array($item['details'], array_column($carry, 'district'))
            ) {
                $carry[] = $item;
            }
            return $carry;
        }, $kladrs);

        // $result = array_filter(array_merge($dispatchPoints, $kladrs), function ($item) use ($dispatchPoints) {
        //     return!in_array($item['id'], array_column($dispatchPoints, 'id'));
        // });
        // return response(['count($result)' => count($result)]);
        for($i = 0; $i < count($result); $i++){
            $result[$i]['keyId'] = $i+1;
        }

        // foreach($result as $key => $point){
            // $point['keyId'] = $key+1;
        // }

        return response([
            'dispatchPoints' => $result,
        ]);
        return response([
            'kladrs' => $kladrs,
            'dispatchPoints' => $dispatchPoints
        ]);

        // return response([
        //     'kladrs' => Kladr::has('dispatchPoints', '>=', 1)->with('dispatchPoints')->get(),
        //     'dispatchPoints' => $dispatchPoints1->concat($dispatchPoints2)
        // ]);
    }
}