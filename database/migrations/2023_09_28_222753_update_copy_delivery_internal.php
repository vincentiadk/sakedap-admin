<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCopyDeliveryInternal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('copy_delivery_internals', function (Blueprint $table) {
            $table->integer('accepted_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('copy_delivery_internals', function (Blueprint $table) {
            $table->string('accepted_date')->nullable(false)->change();
        });
    }
}
