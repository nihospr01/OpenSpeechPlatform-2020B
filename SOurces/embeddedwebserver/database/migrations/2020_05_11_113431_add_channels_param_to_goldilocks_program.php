<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChannelsParamToGoldilocksProgram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('goldilocks_programs', function($table) {
            $table->text('parametersTwoChannels')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('goldilocks_programs', function($table) {
            $table->dropColumn('parametersTwoChannels');
        });
    }
}
