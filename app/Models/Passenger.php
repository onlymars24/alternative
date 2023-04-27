<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;
    protected $fillable = [            
        'surname',
        'name',
        'patronymic',
        'gender',
        'birth_date',
        'citizenship',
        'doc_number',
        'doc_series',
        'doc_type',
        'ticket_type',
        'user_id'
    ];
}
