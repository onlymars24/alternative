<?php

namespace Database\Seeders;

use App\MyApp;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();
        $roles = MyApp::ROLES;

        foreach($roles as $role){
            Role::create($role);
        }
    }
}
