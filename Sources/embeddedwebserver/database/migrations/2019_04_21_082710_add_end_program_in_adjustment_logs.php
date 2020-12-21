<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndProgramInAdjustmentLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listener_adjustment_logs', function($table) {
            $table->integer('end_program_id')->unsigned()->nullable();
            $table->foreign('end_program_id')->references('id')->on('goldilocks_programs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listener_adjustment_logs', function($table) {
            $table->dropForeign('listener_adjustment_logs_end_program_id_foreign');
            $table->dropColumn('end_program_id');
        });
    }
}
