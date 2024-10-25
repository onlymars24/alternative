<?php

namespace App\Http\Controllers;

use App\Models\Kladr;
use Illuminate\Http\Request;
use App\Services\SlugService;
use App\Services\KladrService;

class CustomKladrController extends Controller
{
    public function filter(Request $request){
        return response([
            'kladrs' => Kladr::where([['name', 'like', '%'.$request->customKladrFilter.'%'], ['custom', '=', true]])->get()
        ]);
    }

    public function create(Request $request){
        $kladr = Kladr::create([
            'name' => $request->name,
            'slug' => SlugService::create($request->name),
            'code' => $request->code,
            'custom' => true
        ]);
        if($kladr->code && strlen($kladr->code) == 13){
            KladrService::decode($kladr);
        }
        
        $kladr->socr = $request->socr && $request->socr != $kladr->socr ? $request->socr : $kladr->socr;
        $kladr->region = $request->region && $request->region != $kladr->region ? $request->region : $kladr->region;
        $kladr->city = $request->city && $request->city != $kladr->city ? $request->city : $kladr->city;
        $kladr->district = $request->district && $request->district != $kladr->district ? $request->district : $kladr->district;
        $kladr->relevance = $request->relevance && $request->relevance != $kladr->relevance ? $request->relevance : $kladr->relevance;
        $kladr->save();

        return response(['kladr' => $kladr]);
    }

    public function one(Request $request){
        return response(['kladr' => Kladr::find($request->kladrId)]);
    }

    public function edit(Request $request){
        $kladr = Kladr::find($request->id);
        $kladr->name = $request->name;
        $kladr->slug = SlugService::create($request->name);
        $kladr->code = $request->code;
        $kladr->save();
        if($kladr->code && strlen($kladr->code) == 13){
            KladrService::decode($kladr);
        }
        $kladr->socr = $request->socr && $request->socr != $kladr->socr ? $request->socr : $kladr->socr;
        $kladr->region = $request->region && $request->region != $kladr->region ? $request->region : $kladr->region;
        $kladr->city = $request->city && $request->city != $kladr->city ? $request->city : $kladr->city;
        $kladr->district = $request->district && $request->district != $kladr->district ? $request->district : $kladr->district;
        $kladr->relevance = $request->relevance && $request->relevance != $kladr->relevance ? $request->relevance : $kladr->relevance;
        $kladr->save();
    }

    public function delete(Request $request){
        $kladr = Kladr::find($request->kladrId);
        $kladr->delete();
    }
}