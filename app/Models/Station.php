<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'sourceId',
        'name',
        'slug',
        'region',
        'address',
        'longitude',
        'latitude',
        'contacts',
        'kladr_id',
        'depotId'
    ];

    public function kladr()
    {
        return $this->belongsTo(Kladr::class);
    }

    public function dispatchPoints()
    {
        return $this->hasMany(DispatchPoint::class);
    }
    
    public function kladrStationPage()
    {
        return $this->hasOne(KladrStationPage::class);
    }

    public function arrivalPoints()
    {
        return $this->hasMany(CacheArrivalPoint::class);
    }
}