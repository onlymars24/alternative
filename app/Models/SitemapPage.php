<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitemapPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'changefreq'
    ];
}
