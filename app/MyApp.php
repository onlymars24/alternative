<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyApp
{
    const ROLES = [
        [
            'id' => 1,
            'name' => 'Пользователь'
        ],
        [
            'id' => 2,
            'name' => 'Администратор'
        ],
        [
            'id' => 3,
            'name' => 'Редактор'
        ],
    ];

    const ROLE_PHONE = '+7 (777) 777 7777';

    const META_IMG_DEFAULT = 'meta/default.jpg';
}