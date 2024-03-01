<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'orderPointId',
        'orderPointName',
        'matchPointId',
        'matchPointName',
        'pointType'
    ];
}
