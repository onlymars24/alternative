<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KladrStationPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'settlement_name',
        'url_settlement_name',
        'url_region_code',
        'content',
        'contacts',
        'map',
        'hidden',
        'header_img',
        'kladr_id',
        'station_id',
    ];

    public function kladr()
    {
        return $this->belongsTo(Kladr::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

}