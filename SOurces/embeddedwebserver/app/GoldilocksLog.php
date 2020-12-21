<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldilocksLog extends Model
{
    protected $fillable = ['listener_id', 'action', 'lvh_values', 'program_state'];
}
