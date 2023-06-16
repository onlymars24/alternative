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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticketCode')->nullable();
            $table->string('ticketNum')->nullable();
            $table->string('ticketSeries')->nullable();
            $table->enum('ticketClass', ['P', 'B']);
            $table->string('ticketTypeCode')->nullable();
            $table->string('ticketType')->nullable();
            $table->string('raceUid')->nullable();
            $table->string('raceNum')->nullable();
            $table->string('raceName')->nullable();
            $table->integer('raceClassId')->nullable();
            $table->string('dispatchDate')->nullable();
            $table->string('dispatchStation')->nullable();
            $table->string('dispatchAddress')->nullable();
            $table->string('arrivalDate')->nullable();
            $table->string('arrivalStation')->nullable();
            $table->string('arrivalAddress')->nullable();
            $table->string('seat')->nullable();
            $table->string('platform')->nullable();
            $table->string('lastName')->nullable();
            $table->string('firstName')->nullable();
            $table->string('middleName')->nullable();
            $table->string('docTypeCode')->nullable();
            $table->string('docType')->nullable();
            $table->string('docSeries')->nullable();
            $table->string('docNum')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('gender')->nullable();
            $table->string('birthday')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('supplierCurrencyCode')->nullable();
            $table->decimal('supplierFare', 8, 2)->nullable();
            $table->decimal('supplierDues', 8, 2)->nullable();
            $table->decimal('supplierPrice', 8, 2)->nullable();
            $table->decimal('supplierRepayment', 8, 2)->nullable();
            $table->string('currencyCode')->nullable();
            $table->decimal('dues', 8, 2)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('vat', 8, 2)->nullable();
            $table->decimal('repayment', 8, 2)->nullable();
            $table->string('busInfo')->nullable();
            $table->string('carrier')->nullable();
            $table->string('carrierInn')->nullable();
            $table->string('carrierPhone')->nullable();
            $table->string('barcode')->nullable();
            $table->enum('status', ['B', 'S', 'R', 'C']);
            $table->string('returned')->nullable();
            $table->string('benefit')->nullable();
            $table->string('hash')->nullable();
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
        Schema::dropIfExists('tickets');
    }
};
