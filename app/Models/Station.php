<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function dispatchPoint()
    {
        return $this->hasOne(DispatchPoint::class);
    }
    
    public function kladrStationPage()
    {
        return $this->hasOne(KladrStationPage::class);
    }
}