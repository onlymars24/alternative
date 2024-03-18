<?php

namespace App\Services;

use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FtpLoadingService
{    
    public static function put(){
        if(env('APP_ENV') == 'production'){
            $newSitemap = File::get(env('XML_FILE_NAME'));
            $ftp = Storage::disk('sftp')->put('/var/www/rosvokzaly/data/public/sitemap.xml', $newSitemap);
        }
    }
}