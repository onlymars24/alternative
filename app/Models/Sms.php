<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'phone',
        'code',
        'user',
        'type',
        'used',
        'cost',
        'balance'
    ];
}