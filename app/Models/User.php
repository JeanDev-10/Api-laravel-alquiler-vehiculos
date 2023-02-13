<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
    ];
    public $timestamps=false;
    /* $role = Role::create(['name' => 'writer']);
    $permission = Permission::create(['name' => 'edit articles']); */
    public function vehiculos(){
        return $this->belongsToMany(Vehiculo::class,'cliente_vehiculo');
    }
}
