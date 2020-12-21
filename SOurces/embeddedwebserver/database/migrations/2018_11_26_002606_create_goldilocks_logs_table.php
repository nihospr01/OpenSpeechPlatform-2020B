<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoldilocksLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goldilocks_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listener_id')->unsigned();
            $table->foreign('listener_id')->references('id')->on('goldilocks_listeners')->onDelete('cascade');
            $table->string('action');
            $table->text('lvh_values')->nullable();
            $table->text('program_state')->nullable();
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
        Schema::dropIfExists('goldilocks_logs');
    }
}
