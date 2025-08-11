<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColletionFavouritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('collection_favourites', function (Blueprint $table) {
            $table->id();
            $table->string('pemustaka_id');
            $table->foreignId('collection_id')->constrained('collections');
            $table->timestamp('tanggal_favorit');
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
        Schema::dropIfExists('colletion_favourites');
    }
}
