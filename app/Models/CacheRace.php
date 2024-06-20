<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacheRace extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'dispatchPointName',
        'arrivalPointName',
        'list'
    ];
}
