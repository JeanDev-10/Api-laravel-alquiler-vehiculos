<?php

namespace Database\Seeders;

use App\Models\ClienteVehiculo;
use App\Models\DetalleAlquiler;
use App\Models\User;
use App\Models\Vehiculo;
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
            "password"=>bcrypt('jean123'),
            "cedula"=>"1313626440"
        ])->assignRole('admin');
        User::create([
            "name"=>"miguelito rodriguez",
            "email"=>"miguel@hotmail.com",
            "password"=>bcrypt('miguel123'),
            "cedula"=>"1234567877"
        ])->assignRole('cliente');
        ClienteVehiculo::create([
            "vehiculo_id"=>1,
            "user_id"=>2
        ]);
        DetalleAlquiler::create([
            "fecha_alquiler"=>"2023-01-02",
            "tiempo_alquiler"=>"2023-01-04",
            "valor_alquiler"=>100,
            "cliente_vehiculo_id"=>1,
        ]);
        $vehiculo=Vehiculo::find(1);
        $vehiculo->estado=0;
        $vehiculo->save();
    }
}
