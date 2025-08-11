<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Marks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->integer('deposit_head_id');
            $table->integer('province_id');
            $table->integer('regency_id');
            $table->string('year');
            $table->integer('last_digit');
            $table->integer('missing_digit');
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->index(['deposit_head_id', 'province_id', 'regency_id', 'year', 'last_digit']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marks');
    }
}
