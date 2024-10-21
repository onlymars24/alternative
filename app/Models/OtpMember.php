<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 
        'user_id',
        'expired_at',
    ];
}
