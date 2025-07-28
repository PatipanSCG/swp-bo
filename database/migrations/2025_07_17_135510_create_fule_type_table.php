<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('Stations', function (Blueprint $table) {
            $table->id('StationID');
            $table->string('StationName');
            $table->integer('TaxID');
            $table->integer('BrandID');
            $table->string('Address');
            $table->integer('Province');
            $table->integer('Distric');
            $table->integer('Subdistric');
            $table->string('Postcode');
            $table->string('last');
            $table->string('long');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Customers', function (Blueprint $table) {
            $table->id('CustomerID');
            $table->integer('CustomerType');
            $table->string('CustomerName');
            $table->integer('TaxID');
            $table->string('Address');
            $table->integer('Province');
            $table->integer('Distric');
            $table->integer('Subdistric');
            $table->string('Postcode');
            $table->string('Telphone');
            $table->string('Email');
            $table->string('Phone');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Dispenser', function (Blueprint $table) {
            $table->id('DispenserID');
            $table->integer('StationID');
            $table->integer('BrandID');
            $table->string('Model');
            $table->date('LastCalibationDate');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Nozzle', function (Blueprint $table) {
            $table->id('NozzleID');
            $table->integer('DispenserID');
            $table->string('NozzleNumber');
            $table->integer('FuleTypeID');
            $table->integer('FlowRate');
            $table->date('LastCalibationDate');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Modal', function (Blueprint $table) {
            $table->id('ModalID');
            $table->integer('BrandID');
            $table->string('ModelName');
            
            $this->addCommonColumns($table);
        });

        Schema::create('FuleType', function (Blueprint $table) {
            $table->id('FuleTypeID');
            $table->string('FuleTypeName');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Brand', function (Blueprint $table) {
            $table->id('BrandID');
            $table->string('BrandName');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Contact', function (Blueprint $table) {
            $table->id('ContactID');
            $table->integer('StationID');
            $table->integer('CustomerID');
            $table->string('ContactName');
            $table->string('ContactEmail');
            $table->string('ContactPhone');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Comunicatae', function (Blueprint $table) {
            $table->id('ComunicataeID');
            $table->integer('StationID');
            $table->integer('CustomerID');
            $table->integer('UserID');
            $table->integer('ComunicataeTypeID');
            $table->text('ComunicataeDetail');
            
            $this->addCommonColumns($table);
        });

        Schema::create('ComunicataeType', function (Blueprint $table) {
            $table->id('ComunicataeTypeID');
            $table->string('ComunicataeName');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Work', function (Blueprint $table) {
            $table->id('WorkID');
            $table->integer('StationID');
            $table->integer('CustomerID');
            $table->integer('UserCreate');
            $table->date('AppointmentDate');
            $table->decimal('distance', 10, 2);
            $this->addCommonColumns($table);
        });

        Schema::create('WorkEmployee', function (Blueprint $table) {
            $table->id('WorkEmployeeID');
            $table->integer('WorkID');
            $table->integer('UserID');
            $this->addCommonColumns($table);
        });

        Schema::create('WIRActivity', function (Blueprint $table) {
            $table->id('WIRAID');
            $table->text('Detail');
            $table->integer('UserID');
            $this->addCommonColumns($table);
        });

        Schema::create('WorkInspectionRecord', function (Blueprint $table) {
            $table->id('WIRID');
            $table->integer('WorkID');
            $table->integer('StationID');
            $table->integer('DispenserID');
            $table->integer('NozzleID');
            $table->string('NozzleNumber');
            $table->string('MitterBegin');
            $table->string('MitterEnd');
            $table->string('BeforADJ_5L');
            $table->string('BeforADJ_20L');
            $table->string('AfterADJ_5L_1');
            $table->string('AfterADJ_5L_2');
            $table->string('AfterADJ_5L_3');
            $table->string('AfterADJ_5L_MPE');
            $table->string('AfterADJ_20L_1');
            $table->string('AfterADJ_20L_2');
            $table->string('AfterADJ_20L_3');
            $table->string('AfterADJ_20L_MPE');
            $table->string('Condition_MPE');
            $table->string('Condition_DevitionValue');
            $table->string('Condition_DevitionRange');
            $table->string('Condition_Retest20L');
            $table->string('Summary_Correct');
            $table->string('Summary_Wrong');
            $table->string('Standard');
            $table->string('Pliers');
            $table->string('CerMark_Pliers');
            $table->string('CerMark_Seal');
            $table->string('ExpirationDate');
            $table->string('K_factor');
            $this->addCommonColumns($table);
        });

        Schema::create('WorkNozzle', function (Blueprint $table) {
            $table->id('WorkNozzleID');
            $table->integer('WorkID');
            $table->integer('StationID');
            $table->integer('DispenserID');
            $table->integer('NozzleID');
            $table->integer('WIRID');
            
            $this->addCommonColumns($table);
        });

        Schema::create('Note', function (Blueprint $table) {
            $table->id('NoteID');
            $table->integer('WorkID');
            $table->integer('ComunicataeID');
            $table->text('Detail');
            $table->integer('UserID');
            $this->addCommonColumns($table);
        });
    }

    protected function addCommonColumns(Blueprint $table)
    {
        $table->integer('Status');
        $table->timestamps(); // created_at, updated_at
        $table->integer('created_by')->nullable();
        $table->integer('updated_by')->nullable();
    }

    public function down()
    {
        $tables = [
            'Stations', 'Customers', 'Dispenser', 'Nozzle', 'Modal', 'FuleType', 'Brand', 'Contact',
            'Comunicatae', 'ComunicataeType', 'Work', 'WorkEmployee', 'WIRActivity',
            'WorkInspectionRecord', 'WorkNozzleID', 'Note'
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
