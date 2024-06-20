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
        Schema::create('cache_races', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('dispatchPointName')->nullable();
            $table->string('arrivalPointName')->nullable();
            $table->json('list')->nullable();
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
        Schema::dropIfExists('cache_races');
    }
};
