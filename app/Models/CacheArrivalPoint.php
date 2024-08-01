<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacheArrivalPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_point_id',
        'arrival_point_id',
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

    public function kladr(){
        return $this->belongsTo(Kladr::class);
    }

    public function dispatchPoint(){
        return $this->belongsTo(DispatchPoint::class);
    }
}
