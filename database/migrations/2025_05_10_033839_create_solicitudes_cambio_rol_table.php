<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateSolicitudesCambioRolTable extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_cambio_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('rol_solicitado', ['tendero', 'gestor_despacho']);
            $table->string('nombre_empresa')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitudes_cambio_rol');
    }
}
