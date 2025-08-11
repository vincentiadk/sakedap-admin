<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('type', 3)->change();
        });
        Schema::table('contributors', function (Blueprint $table) {
            $table->string('type', 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('type', 1)->change();
        });
        Schema::table('contributors', function (Blueprint $table) {
            $table->string('type', 1)->change();
        });
    }
}
