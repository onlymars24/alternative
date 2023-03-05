<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/aptest', function () {
    // $regions = Http::withHeaders([
    //     'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
    // ])->get('https://cluster.avtovokzal.ru/gdstest/rest/regions/643')->object();
    // $points = [];
    // foreach($regions as $region){
    //     $pointsTemp = Http::withHeaders([
    //         'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
    //     ])->get('https://cluster.avtovokzal.ru/gdstest/rest/dispatch_points/'.$region->id)->object();
    //     if($pointsTemp){
    //         foreach($pointsTemp as $point){
    //             $points[] = $point;
    //         }   
    //     }
    // }
    // dd($points); 
});