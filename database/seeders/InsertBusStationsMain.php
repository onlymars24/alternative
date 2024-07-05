<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
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
