<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoldilocksGenericTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goldilocks_generic', function (Blueprint $table) {
            $table->increments('id');
            $table->text('parameters');
            $table->timestamps();
        });

        $genericParams = '{"targets":[59,61,62,73,75,64],"ltass":[44,58,57,55,56,57],"hearing_loss":[25,30,35,45,55,85],"compression_ratio":[1.4,1.4,1.4,1.4,1.4,1.4],"g50":[19.3,7.3,9.3,22.3,23.3,11.3],"g65":[15,3,5,18,19,7],"g80":[10.7,-1.3,0.7,13.7,14.7,2.7],"multiplier_l":[3,3,0,0,0,0],"multiplier_h":[0,0,0,3,3,3],"g50_max":[35,35,35,35,35,35],"knee_low":[45,45,45,45,45,45],"mpo_band":[110,110,110,110,110,110],"attack":[5,5,5,5,5,5],"release":[20,20,20,20,20,20],"gain":-20,"l_min":-40,"l_max":40,"l_step":1,"v_min":-40,"v_max":40,"v_step":3,"h_min":-40,"h_max":40,"h_step":1,"control_via":0,"afc":1,"sequence_num":3,"sequence_order":0,"app_behavior":0}';

        DB::table('goldilocks_generic')->insert(
            array(
                'parameters' => $genericParams
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goldilocks_generic');
    }
}
