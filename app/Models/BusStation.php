<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'url_settlement_name',
        'url_region_code',
        'content',
        'contacts',
        'dispatch_point_id',
        'kladr_id',
        'hidden',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function kladr()
    {
        return $this->belongsTo(Kladr::class);
    }

    public function dispatchPoint()
    {
        return $this->belongsTo(DispatchPoint::class);
    }
}