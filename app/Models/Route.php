<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'num',
        'name',
        'schedule',
        'stops',
        'minPrice',
        'station_id',
        'kladr_id',
        'lastCheckDate'
    ];


    public function kladrs_couples()
    {
        return $this->belongsToMany(KladrsCouple::class);
    }
}
