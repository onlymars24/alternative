<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AllPointsController extends Controller
{
    public function all(){
        $allPoints = Setting::where('name', 'allPoints')->first()->data;
        return json_decode($allPoints);
    }
}
