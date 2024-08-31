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
        Schema::create('kladr_station_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('url_settlement_name');
            $table->string('url_region_code');
            $table->text('content')->nullable();
            $table->text('contacts')->nullable();
            $table->boolean('hidden');

            $table->integer('station_id')->nullable();
            $table->integer('kladr_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kladr_station_pages');
    }
};
