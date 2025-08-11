<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_form', function (Blueprint $table) {
            $table->id();
            $table->integer('expedition_id');
            $table->integer('publisher_id');
            $table->integer('library_id');
            $table->integer('accepted_by');
            $table->date('delivery_date');
            $table->date('accepted_date');
            $table->string('receipt_no');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_form');
    }
}
