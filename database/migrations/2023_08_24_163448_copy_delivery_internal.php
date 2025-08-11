<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CopyDeliveryInternal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('copy_delivery_internals', function (Blueprint $table) {
            $table->id();
            $table->date('delivery_internal_date');
            $table->dateTime('accepted_date');
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->foreignId('system_id')->nullable()->constrained('systems');
            $table->foreignId('collection_copy_id')->nullable()->constrained('collection_copies');
            $table->foreignId('user_delivery_id')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('copy_delivery_internals');
    }
}
