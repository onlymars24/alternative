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
            $table->after('id', function($table){
                $table->string('sourceId')->nullable();
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
            $table->dropColumn(['sourceId']);
        });
    }
};
