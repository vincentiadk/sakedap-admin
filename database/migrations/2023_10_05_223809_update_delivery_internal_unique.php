<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeliveryInternalUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('copy_delivery_internals', function (Blueprint $table) {
            $table->unique('collection_copy_id');
            $table->date('accepted_date')->change();
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
            // Remove the unique constraint if needed
            $table->dropUnique('collection_copy_id');
            $table->integer('accepted_date')->change();
        });
    }
}
