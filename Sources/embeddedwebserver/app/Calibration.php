<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calibration extends Model
{
    protected $fillable = ['name', 'parameters', 'default', 'calibratedParams'];
    protected $table = 'calibrations';

    public function getString(){
        return $this->parameters;
    }

    public function getAPIString(){
        $data = json_decode($this->parameters);
        $formatted_data = [
            'left' => [
                'g65' => $data->left->g65,
                'knee_low' => $data->left->knee_low,
                'mpo_band' => $data->left->mpo_band,
                'attack' => $data->left->attack,
                'release' => $data->left->release,
            ],
            'right' => [
                'g65' => $data->right->g65,
                'knee_low' => $data->right->knee_low,
                'mpo_band' => $data->right->mpo_band,
                'attack' => $data->right->attack,
                'release' => $data->right->release,
            ]
        ];
        return $formatted_data;
    }
}
