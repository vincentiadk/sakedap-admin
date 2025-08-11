<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCollectionLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('collection_locations', 'collection_copies');
        Schema::table('collection_copies', function (Blueprint $table) {
            $table->integer('delivery_form_id')->nullable();;
            $table->string('availability')->nullable();
            $table->dropColumn('copy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('collection_copies', 'collection_locations');
        Schema::table('collection_locations', function (Blueprint $table) {
            $table->integer('copy')->nullable();
            $table->dropColumn('delivery_form_id');
            $table->dropColumn('availability');
        });
    }
}
