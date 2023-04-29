<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertCountries extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = Http::withHeaders([
            'Authorization' => 'Basic YWx0NzAxNzQ3OTY4MDpEYlhqRk0zQWZV',
        ])->get('https://cluster.avtovokzal.ru/gdstest/rest/countries')->object();
        Setting::create([
            'name' => 'countries',
            'data' => json_encode($countries)
        ]);
    }
}
