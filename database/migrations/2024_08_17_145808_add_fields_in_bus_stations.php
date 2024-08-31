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
        Schema::table('bus_stations', function (Blueprint $table) {
            $table->after('data', function($table){
                $table->string('url_settlement_name');
                $table->string('url_region_code');
                $table->string('address')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->longText('contacts')->nullable();
                $table->longText('content')->nullable();
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
        Schema::table('bus_stations', function (Blueprint $table) {
            $table->dropColumn(['url_settlement_name', 'url_region_code', 'address', 'latitude', 'longitude', 'contacts', 'content']);
        });
    }
};
