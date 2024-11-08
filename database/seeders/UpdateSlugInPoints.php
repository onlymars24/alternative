<?php

namespace Database\Seeders;

use App\Models\DispatchPoint;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UpdateSlugInPoints extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dispatchPoints = DispatchPoint::all();

        foreach($dispatchPoints as $dispatchPoint){
            
        }
    }
}
