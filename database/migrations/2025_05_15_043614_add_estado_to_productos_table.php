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
    Schema::table('productos', function (Blueprint $table) {
        $table->string('estado')->default('activo')->after('distribuidor_id');
        // Puedes ajustar el tipo de dato y posición según necesites
    });
}

public function down()
{
    Schema::table('productos', function (Blueprint $table) {
        $table->dropColumn('estado');
    });
}

};
