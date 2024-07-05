<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\BusStation;
use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use App\Services\FtpLoadingService;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertBusStationsMain extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $busStationsMain = Setting::where('name', 'busStationsMain')->first();
        if($busStationsMain){
            $busStationsMain->delete();
        }
        Setting::create([
            'name' => 'busStationsMain',
            'data' => '[]'
        ]);
    }
}
