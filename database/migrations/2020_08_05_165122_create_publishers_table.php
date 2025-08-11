<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('publishers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations');
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('village_id')->nullable()->constrained('villages');
            $table->string('photo')->nullable();
            $table->char('publisher_code', 14)->nullable()->unique();
            $table->string('contact')->nullable();
            $table->string('fax')->nullable();
            $table->string('name');
            $table->string('name_change')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('website')->nullable();
            $table->text('address')->nullable();
            $table->char('postal_code', 5)->nullable();
            $table->char('type', 1);
            $table->char('system_type', 4)->nullable();
            $table->char('code_system', 10)->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('statement_letter')->nullable();
            $table->char('status', 1);
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
        Schema::dropIfExists('publishers');
    }
}
