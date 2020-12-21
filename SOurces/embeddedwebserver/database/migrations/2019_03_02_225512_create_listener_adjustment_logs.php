<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListenerAdjustmentLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listener_adjustment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listener_id')->unsigned();
            $table->foreign('listener_id')->references('id')->on('goldilocks_listeners')->onDelete('cascade');
            $table->integer('researcher_id')->unsigned();
            $table->foreign('researcher_id')->references('id')->on('goldilocks_researchers')->onDelete('cascade');
            $table->text('final_lvh')->nullable();
            $table->integer('steps');
            $table->float('seconds_elapsed');
            $table->text('change_string')->nullable();
            $table->text('starting_g65')->nullable();
            $table->text('compression_ratio')->nullable();
            $table->text('l_multipliers')->nullable();
            $table->text('h_multipliers')->nullable();
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
        Schema::dropIfExists('listener_adjustment_logs');
    }
}
