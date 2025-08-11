<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CollectionLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('lib_loc_id');
            $table->integer('collection_id');
            $table->integer('copy');
            $table->string('condition')->comment('1 = Sangat Baik, 2 = Baik, 3 = Cukup, 4 = Buruk');;
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->index(['lib_loc_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collections_locations');
    }
}
