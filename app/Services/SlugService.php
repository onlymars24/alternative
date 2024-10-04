<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\FermaEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SlugService
{
    public static function create($string){
        return str_replace([' ', '/', '\'', '"', '.', ',', '<', '>', '.', ','], ['_', '-', '-', '', '', '', '', '',  '', ''], $string);
    }
}