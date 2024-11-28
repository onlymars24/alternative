<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kladr extends Model
{
    use HasFactory;

    protected $fillable = [
        'sourceId',
        'name',
        'region',
        'city',
        'district',
        'slug',
        'socr',
        'code',
        'index',
        'gninmb',
        'uno',
        'ocatd',
        'status',
        'custom',
    ];

    public function dispatchPoints(){
        return $this->hasMany(DispatchPoint::class);
    }

    public function arrivalPoints(){
        return $this->hasMany(CacheArrivalPoint::class);
    }

    public function busStation()
    {
        return $this->hasOne(BusStation::class);
    }

    public function kladrStationPage()
    {
        return $this->hasOne(KladrStationPage::class);
    }

    public function stations(){
        return $this->hasMany(Station::class);
    }
}