<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameStartProgramInAdjustmentLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listener_adjustment_logs', function($table) {
            $table->dropForeign('listener_adjustment_logs_program_id_foreign');
            $table->renameColumn('program_id', 'start_program_id');
            $table->foreign('start_program_id')->references('id')->on('goldilocks_programs')->onDelete('cascade');
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
            $table->dropForeign('listener_adjustment_logs_start_program_id_foreign');
            $table->renameColumn('start_program_id', 'program_id');
            $table->foreign('program_id')->references('id')->on('goldilocks_programs')->onDelete('cascade');
        });
    }
}
