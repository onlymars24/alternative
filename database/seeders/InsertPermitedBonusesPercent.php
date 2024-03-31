<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertPermitedBonusesPercent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Setting::where('name', 'bonusesPercent')->first();
        if($setting){
            $setting->delete();
        }
        Setting::create([
            'name' => 'bonusesPercent',
            'data' => '{"bonusesPercent": 10}'
        ]);
    }
}
