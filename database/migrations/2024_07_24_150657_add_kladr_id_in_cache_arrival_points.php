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
        Schema::table('cache_arrival_points', function (Blueprint $table) {
            $table->after('id', function ($table) { 
                $table->integer('arrival_point_id');
                $table->string('name');
                $table->string('region');
                $table->string('details')->nullable();
                $table->string('address')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->string('okato');
                $table->boolean('place');
                $table->integer('kladr_id')->nullable();
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
        Schema::table('cache_arrival_points', function (Blueprint $table) {
            $table->dropColumn(['arrival_point_id', 'name', 'region', 'details', 'address', 'latitude', 'longitude', 'okato', 'place', 'kladr_id']);
        });
    }
};
