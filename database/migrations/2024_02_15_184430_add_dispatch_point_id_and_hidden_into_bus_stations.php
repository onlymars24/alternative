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
            $table->after('data', function ($table) {
                $table->integer('dispatch_point_id');
                $table->boolean('hidden')->default(true);
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
            $table->dropColumn(['dispatch_point_id', 'hidden']);
        });
    }
};
