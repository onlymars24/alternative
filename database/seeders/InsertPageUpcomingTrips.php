<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertPageUpcomingTrips extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pageUpcomingTrips = Setting::where('name', 'pageUpcomingTrips')->first();
        if($pageUpcomingTrips){
            $pageUpcomingTrips->delete();
        }
        Setting::create([
            'name' => 'pageUpcomingTrips',
            'data' => '{"content": "<p>Контент для ЛК</p>"}'
        ]);
    }
}
