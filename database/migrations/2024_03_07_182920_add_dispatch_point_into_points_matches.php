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
        Schema::table('points_matches', function (Blueprint $table) {
            $table->after('pointType', function ($table) {
                $table->integer('dispatchPointId')->nullable();
                $table->string('dispatchPointName')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points_matches', function (Blueprint $table) {
            $table->dropColumn(['dispatchPointId', 'dispatchPointName']);
        });
    }
};
