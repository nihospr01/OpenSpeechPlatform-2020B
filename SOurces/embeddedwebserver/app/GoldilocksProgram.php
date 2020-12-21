<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldilocksProgram extends Model
{
    protected $fillable = ['listener_id', 'name', 'parameters', 'name_rank', 'parametersTwoChannels'];

    public function listener()
    {
        return $this->belongsTo('App\GoldilocksListener', 'listener_id');
    }

    public function getString()
    {
        return $this->parameters;
    }

    public function getNameAttribute($value)
    {
        $rank = $this->name_rank;

        if ($rank != 1) {
            $value = $value . "-" . $rank;
        }

        return $value;
    }

    // NOT IN USE
    public function getAPIString()
    {
        $data = json_decode($this->parametersTwoChannels);
        $data_single = json_decode($this->parameters);
        $formatted_data = [
            'left' => [
                'en_ha' => 1,
                'afc' => $data_single->afc,
                'rear_mics' => 0,
                'g50' => $data->left->g50,
                'g80' => $data->left->g80,
                'knee_low' => $data->left->knee_low,
                'mpo_band' => $data->left->mpo_band,
                'attack' => $data->left->attack,
                'release' => $data->left->release,
                'gain' => $data_single->gain,
            ],
            'right' => [
                'en_ha' => 1,
                'afc' => $data_single->afc,
                'rear_mics' => 0,
                'g50' => $data->right->g50,
                'g80' => $data->right->g80,
                'knee_low' => $data->right->knee_low,
                'mpo_band' => $data->right->mpo_band,
                'attack' => $data->right->attack,
                'release' => $data->right->release,
                'gain' => $data_single->gain,
            ]
        ];
        return $formatted_data;
    }
}
