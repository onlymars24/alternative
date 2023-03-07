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
    // $arrival_points = Http::withHeaders([
    //     'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
    // ])->get('https://cluster.avtovokzal.ru/gdstest/rest/arrival_points/1171')->object();
    // dd(json_encode($arrival_points)); 
    $january = new DateTime('2023-03-10 08:05:00');
    $february = new DateTime('2023-03-10 13:05:00');
    $interval = $february->diff($january);
    
    dd($interval);
    dd($interval->format('%h час. %i мин.'));
});