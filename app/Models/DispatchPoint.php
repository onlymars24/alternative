<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'region',
        'details',
        'address',
        'latitude',
        'longitude',
        'okato',
        'place',
        'kladr_id',
        'station_id'
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function kladr(){
        return $this->belongsTo(Kladr::class);
    }
}
