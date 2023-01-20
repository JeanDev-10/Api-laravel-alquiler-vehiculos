<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'cliente']);
        Permission::create(['name' => 'index.vehiculos'])->syncRoles([$role1,$role2]);
        Permission::create(['name' => 'update.vehiculos'])->syncRoles([$role1]);
        Permission::create(['name' => 'destroy.vehiculos'])->syncRoles([$role1]);
        Permission::create(['name' => 'store.vehiculos'])->syncRoles([$role1]);
        Permission::create(['name' => 'alquilados.vehiculos'])->syncRoles([$role1]);
        /* Permission::create(['name' => 'mis-alquilados.vehiculos'])->syncRoles([$role2]);
        Permission::create(['name' => 'alquilar.vehiculos'])->syncRoles([$role2]); */

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
