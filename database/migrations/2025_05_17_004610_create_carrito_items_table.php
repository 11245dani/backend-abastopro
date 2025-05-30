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
    Schema::create('carrito_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('carrito_id')->constrained()->onDelete('cascade');
    $table->foreignId('producto_id')->constrained()->onDelete('restrict');
    $table->integer('cantidad');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrito_items');
    }
};
