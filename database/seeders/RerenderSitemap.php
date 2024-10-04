<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SitemapService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RerenderSitemap extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $xml = simplexml_load_file(public_path(env('XML_FILE_NAME')));
        // SitemapService::add('http://localhost:5173/автобус/Новосибирск/Барнаул', 'dayly');
    }
}
