<?php

use App\Models\Kladr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kladrs_couples', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Kladr::class, 'dispatch_kladr_id');
            $table->foreignIdFor(Kladr::class, 'arrival_kladr_id');
            $table->integer('market_id')->nullable();
            $table->string('market_updated_at')->nullable();
            $table->boolean('racesExistence')->nullable();
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
        Schema::dropIfExists('kladrs_couples');
    }
};
