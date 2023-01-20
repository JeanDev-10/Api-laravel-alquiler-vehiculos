<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_alquiler', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_alquiler');
            $table->dateTime('tiempo_alquiler');
            $table->double('valor_alquiler');
            $table->foreignId('cliente_vehiculo_id')
                ->nullable()
                ->unique()
                ->constrained('cliente_vehiculo')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_articulo');
    }
};
