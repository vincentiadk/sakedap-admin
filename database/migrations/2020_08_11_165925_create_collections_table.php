<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('collections', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_old')->nullable();
            $table->foreignId('publisher_id')->constrained('publishers')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullable();
            $table->bigInteger('parent_id')->default(0);
            $table->text('title')->nullable();
            $table->longText('physical_description')->nullable();
            $table->string('album')->nullable();
            $table->string('slug')->nullable();
            $table->char('type', 1);
            $table->char('type_book', 1)->nullable();
            $table->string('series')->nullable();
            $table->string('edition')->nullable();
            $table->string('serial')->nullable()->comment("1:harian, 2:mingguan, 3:Bulanan, 4:3 bulan sekali, 5:4 bulan sekali, 6:6 bulan sekali, 7:tahunan, 8:2 tahun sekali, 9:3 tahun sekali");
            $table->string('ddc')->nullable()->comment('Klasifikasi desimal dewey dari ISBN');
            $table->string('volume')->nullable()->comment('Diisi jika merupakan child collection');
            $table->char('deposit', 16)->nullable()->unique();
            $table->char('code', 17)->nullable()->comment("1:ISBN, 2:ISMN, 3:ISSN, 4:ISRC");
            $table->char('code_type', 1)->nullable();
            $table->char('code_kdt', 10)->nullable()->comment('Kode Pub Detail -> Isbn');
            $table->string('source')->nullable();
            $table->char('publication_month', 2)->nullable();
            $table->year('publication_year')->nullable();
            $table->string('copyright')->nullable();
            $table->string('preview')->nullable();
            $table->text('description')->nullable();
            $table->text('problem')->nullable();
            $table->boolean('sync')->default(0);
            $table->boolean('lock')->default(0);
            $table->boolean('manual')->default(0);
            $table->date('date')->nullable();
            $table->char('access', 1)->nullable();
            $table->char('status', 1);
            $table->foreignId('manage_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->foreignId('edit_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
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
        Schema::dropIfExists('collections');
    }
}
