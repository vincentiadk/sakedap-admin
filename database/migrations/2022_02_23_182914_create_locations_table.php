<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location')->nullable();
            $table->string('host')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('root')->nullable();
            $table->string('driver')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
        DB::connection('mysql')
            ->table('locations')
            ->insert([
                [
                    'location' => 'storage1',
                    'root' => 'E:/nas/edeposit/app',
                    'driver' => 'local',
                    'active' => false,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ],
                [
                    'location' => 'storage2',
                    'root' => 'F:/storage2/app',
                    'driver' => 'local',
                    'active' => true,
                    'created_at' => date('Y-m-d'),
                    'updated_at' => date('Y-m-d'),
                ],
            ]);
        Schema::table('collection_media', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('collection_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('downloads', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('directors', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('publishers', function (Blueprint $table) {
            $table->unsignedBigInteger('birth_certificate_location')->default(1);
            $table->unsignedBigInteger('statement_letter_location')->default(1);
            $table->foreign('birth_certificate_location')->references('id')->on('locations');
            $table->foreign('statement_letter_location')->references('id')->on('locations');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
