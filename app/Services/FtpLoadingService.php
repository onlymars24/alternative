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
            $newSitemap = File::get(public_path(env('XML_FILE_NAME')));
            $gzdata = gzencode($newSitemap, 9);
            $ftp = Storage::disk('sftp')->put('/var/www/rosvokzaly/data/public/sitemaps/directions.xml.gz', $gzdata);
        }
    }
}