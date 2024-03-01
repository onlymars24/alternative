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
        Schema::create('points_matches', function (Blueprint $table) {
            $table->id();
            $table->integer('orderPointId');
            $table->string('orderPointName');
            $table->integer('matchPointId');
            $table->string('matchPointName');
            $table->enum('pointType', ['Отправление', 'Прибытие']);
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
        Schema::dropIfExists('points_matches');
    }
};
