<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteVehiculo extends Model
{
    public $table="cliente_vehiculo";
    public $timestamps=false;
    use HasFactory;
    protected $fillable=["vehiculo_id","user_id"];
    public function detalle_alquiler()
    {
        return $this->hasOne(DetalleAlquiler::class);
    }
}
