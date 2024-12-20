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
            $table->after('relevance', function($table){
                $table->boolean('custom')->default(false);
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
            $table->dropColumn(['custom']);
        });
    }
};
