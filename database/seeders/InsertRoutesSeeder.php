<?php

namespace Database\Seeders;

use DateTime;
use Exception;
use App\Models\Route;
use App\Models\Setting;
use App\Models\Station;
use App\Models\KladrsCouple;
use App\Models\DispatchPoint;
use App\Services\RaceService;
use App\Services\RouteService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kladrsCouples = KladrsCouple::with('dispatchKladr', 'arrivalKladr')->where([
        ['dispatch_kladr_id', '=', 151370], 
          // ['dispatch_kladr_id', '=', 221627]
        ])
        ->take(4)
        ->get()
        ;
        $output = new ConsoleOutput();
        $totalItems = $kladrsCouples->count(); // Замените на общее количество записей, которые вы собираетесь создать.
        $progressBar = new ProgressBar($output, $totalItems);
        $progressBar->start();

        $routes = [];

        
        foreach($kladrsCouples as $kladrsCouple){
          RouteService::upload($kladrsCouple);
          $progressBar->advance();
        }
  
      $progressBar->finish();
      // dd($routes);
    }
}