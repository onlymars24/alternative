<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertDuesIntoSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Setting::where('name', 'dues')->first();
        if($setting){
            $setting->delete();
        }
        Setting::create([
            'name' => 'dues',
            'data' => '{"clusterDue": 6, "acqSbpDue": 1, "acqCardDue": 2.1}'
        ]);
    }
}
