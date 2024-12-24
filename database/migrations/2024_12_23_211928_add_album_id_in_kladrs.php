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
            $table->after('custom', function($table){
                $table->integer('album_id')->nullable();
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
            $table->dropColumn(['album_id']);
        });
    }
};
