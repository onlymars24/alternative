<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertRobotsTxtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $robotsTxt = Setting::where('name', 'robotsTxt')->first();
        if($robotsTxt){
            $robotsTxt->delete();
        }
        Setting::create([
            'name' => 'robotsTxt',
            'data' => '{"content": ""}'
        ]);
    }
}
