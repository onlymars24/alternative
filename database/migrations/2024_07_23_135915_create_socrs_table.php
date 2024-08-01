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
        Schema::create('socrs', function (Blueprint $table) {
            $table->id();
            $table->string('level')->nullable();
            $table->string('scname')->nullable();
            $table->string('socrname')->nullable();
            $table->string('kod_t_st')->nullable();
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
        Schema::dropIfExists('socrs');
    }
};
