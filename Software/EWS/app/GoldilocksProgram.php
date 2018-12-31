<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldilocksProgram extends Model
{
    protected $fillable = ['listener_id', 'name', 'parameters'];

    public function listener(){
        return $this->belongsTo('App\GoldilocksListener', 'listener_id');
    }

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
            'knee_high' => $data->mpo_limit,
            'attack' => $data->attack,
            'release' => $data->release
        ];
        return $formatted_data;
    }
}

