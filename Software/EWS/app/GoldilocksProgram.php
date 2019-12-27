<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldilocksProgram extends Model
{
    protected $fillable = ['listener_id', 'name', 'parameters', 'name_rank'];

    public function listener(){
        return $this->belongsTo('App\GoldilocksListener', 'listener_id');
    }

    public function getString(){
        return $this->parameters;
    }

    public function getNameAttribute($value) {
        $rank = $this->name_rank;

        if ($rank != 1) {
            $value = $value . "-" . $rank;
        }

        return $value;
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

