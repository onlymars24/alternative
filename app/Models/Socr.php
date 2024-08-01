<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socr extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'scname',
        'socrname',
        'kod_t_st',
    ];
}
