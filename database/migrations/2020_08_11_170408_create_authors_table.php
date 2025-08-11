<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('authors', function (Blueprint $table) {
            $table->id();
            $table->text('fullname');
            $table->text('title')->nullable()->comment('Nama Marga, Title (S.H., SS, dll)');
            $table->text('slug');
            $table->string('photo')->default('public/main/user.png');
            $table->year('year_of_birth')->nullable();
            $table->year('year_of_death')->nullable();
            $table->integer('count')->default(0);
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
        Schema::dropIfExists('authors');
    }
}
