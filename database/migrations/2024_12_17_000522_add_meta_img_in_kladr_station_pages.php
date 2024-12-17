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
        Schema::table('kladr_station_pages', function (Blueprint $table) {
            $table->string('metaImg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kladr_station_pages', function (Blueprint $table) {
            $table->dropColumn(['metaImg']);
        });
    }
};
