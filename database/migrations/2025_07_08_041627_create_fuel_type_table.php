<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection('sqlsrv_secondary')->create('fuel_type', function (Blueprint $table) {
            $table->increments('FuelTypeID');
            $table->string('FuelTypeName', 100);
            $table->string('OctaneLevel', 10)->nullable();
            $table->boolean('Status')->default(1);
        });
    }

    public function down()
    {
        Schema::connection('sqlsrv_secondary')->dropIfExists('fuel_type');
    }
};
