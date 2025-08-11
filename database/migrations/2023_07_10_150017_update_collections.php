<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->float('price')->nullable();
            $table->string('deposit_head_id')->nullable();
            $table->string('mark_province')->nullable();
            $table->string('mark_national')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('deposit_head_id');
            $table->dropColumn('mark_province');
            $table->dropColumn('mark_national');
        });
    }
}
