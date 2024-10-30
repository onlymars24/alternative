<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertAdPdfIntoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Setting::where('name', 'adPdf')->first();
        if($setting){
            $setting->delete();
        }
        Setting::create([
            'name' => 'adPdf',
            'data' => json_encode(["adPdf" => null])
        ]);
    }
}