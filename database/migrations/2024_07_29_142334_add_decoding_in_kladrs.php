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
        Schema::table('kladrs', function (Blueprint $table) {
            $table->after('status', function ($table) { 
                $table->string('region')->nullable();
                $table->string('city')->nullable();
                $table->string('district')->nullable();
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
        Schema::table('kladrs', function (Blueprint $table) {
            $table->dropColumn(['region', 'city', 'district']);
        });
    }
};
