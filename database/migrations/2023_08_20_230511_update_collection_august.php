<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCollectionAugust extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('collection_copies', function (Blueprint $table) {
            $table->foreignId('edit_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->string('currency')->nullable();
            $table->date('start_publication_date')->nullable();
            $table->date('end_publication_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collection_copies', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('start_publication_date');
            $table->dropColumn('end_publication_date');
        });
    }
}
