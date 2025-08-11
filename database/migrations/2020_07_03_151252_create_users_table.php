<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('users', function (Blueprint $table) {
            $table->id();
            $table->morphs('userable');
            $table->foreignId('library_id')->constrained('libraries')->nullable();
            $table->foreignId('role_id')->constrained('roles')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('lang', ['id', 'en'])->default('id');
            $table->timestamp('last_login')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('enable')->default(1);
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
        Schema::dropIfExists('users');
    }
}
