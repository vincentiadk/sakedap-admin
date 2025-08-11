<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tutorial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tutorial', function (Blueprint $table) {
            $table->id();
            $table->integer('sequence');
            $table->string('title');
            $table->string('category');
            $table->longText('content');
            $table->string('publish');
            $table->timestamps();
            $table->softDeletes('deleted_at');

            $table->index(['title', 'created_at', 'sequence', 'category', 'publish']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tutorial');
    }
}
