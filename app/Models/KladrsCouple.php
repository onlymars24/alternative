<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KladrsCouple extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_kladr_id',
        'arrival_kladr_id',
        'market_id',
        'market_updated_at',
        'racesExistence'
    ];

    public function dispatchKladr(){
        return $this->belongsTo(Kladr::class);
    }

    public function arrivalKladr(){
        return $this->belongsTo(Kladr::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class);
    }
}
