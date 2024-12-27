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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('num')->nullable();
            $table->string('name')->nullable();
            $table->json('schedule')->default('[]');
            $table->json('stops')->default('[]');
            $table->decimal('minPrice', 8, 2)->nullable();
            $table->decimal('maxPrice', 8, 2)->nullable();
            $table->integer('station_id')->nullable();
            $table->integer('kladr_id')->nullable();
            $table->string('lastCheckDate')->nullable();
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
        Schema::dropIfExists('routes');
    }
};
