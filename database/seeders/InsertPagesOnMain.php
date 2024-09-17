<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertPagesOnMain extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pagesOnMain = Setting::where('name', 'pagesOnMain')->first();
        if($pagesOnMain){
            $pagesOnMain->delete();
        }
        Setting::create([
            'name' => 'pagesOnMain',
            'data' => '[]'
        ]);
    }
}
