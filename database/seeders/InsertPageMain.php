<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\MyApp;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InsertPageMain extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pageMain = Setting::where('name', 'pageMain')->first();
        if($pageMain){
            $pageMain->delete();
        }
        Setting::create([
            'name' => 'pageMain',
            'data' => 
            '{
                "content": "<p>Вы находитесь на главной странице сайта</p>",
                "metaImg": "'.MyApp::META_IMG_DEFAULT.'"    
            }'
        ]);
    }
}