<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'whatsapp',
        'sms',
        'server_host',
        'ofd_ferma',
    ];
}
