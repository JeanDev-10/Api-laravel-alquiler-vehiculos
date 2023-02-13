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
        Schema::create('cliente_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')
            ->unique()
            ->constrained('vehiculos')
            ->cascadeOnUpdate()
            ->cascadeOnDelete();
            $table->foreignId('user_id')
            ->constrained('users')
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
        Schema::dropIfExists('cliente_vehiculo');
    }
};
