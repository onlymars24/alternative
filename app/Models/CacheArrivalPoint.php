<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacheArrivalPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_point_id',
        'arrival_points'
    ];
}
