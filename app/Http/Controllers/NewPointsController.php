<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\BusStation;
use Illuminate\Http\Request;
use App\Models\DispatchPoint;
use App\Services\PointService;
use App\Models\CacheArrivalPoint;
use App\Services\FtpLoadingService;
use App\Services\SlugService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class NewPointsController extends Controller
{
    public function get(){
        ini_set('max_execution_time', 600);
        return response([
            'newPoints' => PointService::checkNewPoints()
        ]);
    }

    public function add(Request $request){
        PointService::addNewPoints($request->newPoints);
    }
}