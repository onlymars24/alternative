<?php

namespace App\Models;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_info',
        'user_id',
        'bankOrderId',
        'formUrl',
        'pan',
        'ip',
        'duePercent',
        'duePrice',
        'timezone',
        'dispatchPointId',
        'arrivalPointId',
        'bonusesPrice'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}