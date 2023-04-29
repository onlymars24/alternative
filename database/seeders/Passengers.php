<?php

namespace Database\Seeders;

use App\Models\Passenger;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Passengers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Passenger::create([
            'surname' => 'Сергеев',
            'name' => 'Сергей',
            'patronymic' => 'Сергеевич',
            'gender' => 'M',
            'birth_date' => '2004-10-01',
            'citizenship' => 'ГРУЗИЯ',
            'doc_number' => '666666',
            'doc_series' => '1111',
            'user_id' => 20
        ]);
        Passenger::create([
            'surname' => 'Иванов',
            'name' => 'Иван',
            'patronymic' => 'Иванович',
            'gender' => 'M',
            'birth_date' => '2001-02-09',
            'citizenship' => 'ГРУЗИЯ',
            'doc_number' => '666766',
            'doc_series' => '1111',
            'user_id' => 20
        ]);
    }
}
