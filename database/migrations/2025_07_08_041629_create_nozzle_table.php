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
        Schema::connection('sqlsrv_secondary')->create('nozzle', function (Blueprint $table) {
            $table->increments('NozzleID');
            $table->unsignedInteger('DispenserID');
            $table->integer('NozzleNumber');
            $table->unsignedInteger('FuelTypeID');
            $table->decimal('FlowRate', 8, 2)->nullable();
            $table->boolean('Status')->default(1);
            $table->foreign('DispenserID')->references('DispenserID')->on('dispenser')->onDelete('cascade');
            $table->foreign('FuelTypeID')->references('FuelTypeID')->on('fuel_type')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::connection('sqlsrv_secondary')->dropIfExists('nozzle');
    }
};
