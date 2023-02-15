<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use Database\Factories\VehiculoFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vehiculo::factory(3)->create();
    }
}
