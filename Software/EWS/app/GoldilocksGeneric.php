<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldilocksGeneric extends Model
{
    protected $fillable = ['parameters'];
    protected $table = 'goldilocks_generic';

    public function getString(){
        return $this->parameters;
    }

    public function getAPIString(){
        $data = json_decode($this->parameters);
        $formatted_data = [
            'en_ha' => 1,
            'afc' => $data->afc,
            'rear_mics' => 0,
            'g50' => $data->g50,
            'g80' => $data->g80,
            'knee_low' => $data->knee_low,
            'mpo_band' => $data->mpo_band,
            'attack' => $data->attack,
            'release' => $data->release,
            'gain' => $data->gain,
        ];
        return $formatted_data;
    }
}

