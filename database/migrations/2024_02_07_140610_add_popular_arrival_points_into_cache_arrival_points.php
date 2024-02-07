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
            $table->after('arrival_points', function ($table) {
                $table->json('popular_arrival_points')->default('[]');
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
            $table->dropColumn(['popular_arrival_points']);
        });
    }
};
