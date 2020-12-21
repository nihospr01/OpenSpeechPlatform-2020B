<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoldilocksListenersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goldilocks_listeners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('listener')->unique();
            $table->string('pin');
            $table->integer('researcher_id')->unsigned()->nullable();
            $table->foreign('researcher_id')->references('id')->on('goldilocks_researchers')->onDelete('set null');
            $table->integer('current_program_id')->unsigned()->nullable();
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
        Schema::dropIfExists('goldilocks_listeners');
    }
}
