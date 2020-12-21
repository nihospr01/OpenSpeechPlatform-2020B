<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldilocksResearcher extends Model
{
    protected $fillable = ['researcher'];

    public function listeners(){
        return $this->hasMany('App\GoldilocksListener');
    }
}
