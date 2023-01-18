<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "name"=>"jean rodriguez",
            "email"=>"jean@hotmail.com",
            "password"=>bcrypt('jean123')
        ])->assignRole('admin');
    }
}
