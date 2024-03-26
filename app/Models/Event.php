<?php

namespace App\Models;

use App\Models\BusStation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'descr',
        'content',
        'hidden',
        'image',
        'date',
    ];

    public function bus_stations()
    {
        return $this->belongsToMany(BusStation::class);
    }
}
