<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction',
        'amount',
        'user_phone',
        'user_id',
        'descr',
        'order_id'
    ];
}
