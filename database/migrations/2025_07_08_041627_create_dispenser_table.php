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
        Schema::connection('sqlsrv_secondary')->create('dispenser', function (Blueprint $table) {
            $table->increments('DispenserID');
            $table->unsignedInteger('StationID');
            $table->string('DispenserCode', 50);
            $table->string('FuelType', 50);
            $table->integer('NozzleCount')->default(1);
            $table->string('Brand', 100)->nullable();
            $table->date('LastInspectionDate')->nullable();
            $table->date('PurchaseDate')->nullable();
            $table->date('InstallationDate')->nullable();
            $table->boolean('Status')->default(1);
            $table->foreign('StationID')->references('StationID')->on('station')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::connection('sqlsrv_secondary')->dropIfExists('dispenser');
    }
};
