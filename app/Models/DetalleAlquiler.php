<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAlquiler extends Model
{
    use HasFactory;
    public $timestamps=false;
    public $table="detalle_alquiler";
    protected $fillable=["cliente_vehiculo_id","fecha_alquiler","tiempo_alquiler","valor_alquiler"];

    public function vehiculo_alquiler()
    {
        return $this->belongsTo(VehiculoAlquiler::class);
    }
}
