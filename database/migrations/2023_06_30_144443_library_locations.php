<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LibraryLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('library_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('library_id');
            $table->text('name');
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->index(['library_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('library_locations');
    }
}
