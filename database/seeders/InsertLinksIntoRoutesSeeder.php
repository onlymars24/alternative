<?php

namespace Database\Seeders;

use App\Models\Kladr;
use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertLinksIntoRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = Route::with('kladr')->get();
        foreach($routes as $route){
            if($route->link){
                continue;
            }
            $stops = (array)json_decode($route->stops);
            $lastStop = end($stops);
            if(!isset($lastStop->kladr_id)){
                continue;
            }
            $arrivalKladr = Kladr::find($lastStop->kladr_id);
            $route->busLink = '/автобус/'.$route->kladr->slug.'/'.$arrivalKladr->slug;
            $route->save();
        }
    }
}
