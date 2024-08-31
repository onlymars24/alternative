<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kladr extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'socr',
        'code',
        'index',
        'gninmb',
        'uno',
        'ocatd',
        'status',
    ];

    public function dispatchPoints(){
        return $this->hasMany((DispatchPoint::class));
    }

    public function arrivalPoints(){
        return $this->hasMany((CacheArrivalPoint::class));
    }

    public function busStation()
    {
        return $this->hasOne(BusStation::class);
    }

    public function kladrStationPage()
    {
        return $this->hasOne(KladrStationPage::class);
    }
}