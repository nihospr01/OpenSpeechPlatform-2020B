<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgramIdToProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goldilocks_programs', function($table) {
            // This is to automatically assign the next available rank on
            // "save as" after listener adjustment.
            $table->integer('name_rank')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goldilocks_programs', function($table) {
            $table->dropColumn('name_rank');
        });
    }
}
