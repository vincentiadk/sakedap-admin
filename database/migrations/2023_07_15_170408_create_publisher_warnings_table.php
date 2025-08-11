<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublisherWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_warnings', function (Blueprint $table) {
            $table->id();
            $table->integer('publisher_id')->constrained('publishers');
            $table->integer('library_id')->constrained('libraries')->nullable();
            $table->datetime('warning_date');
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
        Schema::dropIfExists('publisher_warnings');
    }
}
