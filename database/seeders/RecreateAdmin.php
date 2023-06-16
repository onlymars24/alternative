<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RecreateAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminOld = User::where('admin', true)->delete();
        $admin = User::create([
            'phone' => '+7 (777) 777 7777',
            'password' => Hash::make('qwerty123'),
            'admin' => 1
        ]);
    }
}
