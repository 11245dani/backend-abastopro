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
        $table->integer('stock_minimo')->default(10);
    });
}

public function down()
{
    Schema::table('productos', function (Blueprint $table) {
        $table->dropColumn('stock_minimo');
    });
}

};
