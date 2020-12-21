<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoldilocksProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goldilocks_programs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listener_id')->unsigned();
            $table->foreign('listener_id')->references('id')->on('goldilocks_listeners');
            $table->string('name');
            $table->text('parameters');
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
        Schema::dropIfExists('goldilocks_programs');
    }
}
