<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSms extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'type',
        'phone',
        'used',
        'status'
    ];
}
