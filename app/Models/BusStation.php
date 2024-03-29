<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'description',
        'data',
        'dispatch_point_id',
        'hidden',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}