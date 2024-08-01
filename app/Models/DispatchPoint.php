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
        'region',
        'details',
        'address',
        'latitude',
        'longitude',
        'okato',
        'place',
        'kladr_id'
    ];

    public function bus_stations()
    {
        return $this->hasMany(BusStation::class);
    }

    public function kladr(){
        return $this->belongsTo(Kladr::class);
    }
}
