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
        Schema::table('dispatch_points', function (Blueprint $table) {
            $table->after('place', function ($table) {  
                $table->json('popular_arrival_points')->default('[]');
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
        Schema::table('dispatch_points', function (Blueprint $table) {
            $table->dropColumn(['popular_arrival_points', 'kladr_id']);
        });
    }
};
