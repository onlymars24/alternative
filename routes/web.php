<?php

use Nette\Utils\DateTime;
use Illuminate\Support\Facades\Auth;
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


    // $arr = [
    //     [
    //         "firstName" => "Павел",
    //         "lastName" => "Дуров",
    //         "docTypeCode" => "1",
    //         "docNum" => "112233",
    //         "seatCode" => "118551",
    //         "ticketTypeCode" => "1#1#0"
    //     ]
    // ];
    // $arr = json_encode($arr);
    // $arr = json_decode($arr);
    // $response = Http::withHeaders([
    //     'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
    //     'sales' => $arr
    // ])->post('https://cluster.avtovokzal.ru/gdstest/rest/order/book/1770607:558613:20230316:649:16')->object();
    
    // dd($response); 

});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
