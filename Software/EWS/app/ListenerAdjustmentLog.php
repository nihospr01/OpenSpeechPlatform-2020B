<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ListenerAdjustmentLog extends Model
{
    protected $fillable = [
        'listener_id',
        'researcher_id',
        'start_program_id',
        'end_program_id',
        'final_lvh',
        'steps',
        'seconds_elapsed',
        'change_string',
        'starting_g65',
        'ending_g65',
        'compression_ratio',
        'l_multipliers',
        'h_multipliers',
        'client_finish',
        'client_timezone'
    ];

    protected $dates = [
        'client_finish',
    ];

    /**
     * Returns researcher object tied to this log in the database.
     */
    public function researcher(){
        return $this->belongsTo('App\GoldilocksResearcher', 'researcher_id');
    }

    /**
     * Returns listener object tied to this log in the database.
     */
    public function listener(){
        return $this->belongsTo('App\GoldilocksListener', 'listener_id');
    }

    /**
     * Returns starting program object tied to this log in the database.
     */
    public function startProgram(){
        return $this->belongsTo('App\GoldilocksProgram', 'start_program_id');
    }

    /**
     * Returns ending program object tied to this log in the database.
     */
    public function endProgram(){
        return $this->belongsTo('App\GoldilocksProgram', 'end_program_id');
    }

    /**
     * Returns local time of client finish timestamp.
     */
    public function getClientFinishLocalAttribute(){
        return Carbon::parse($this->client_finish)->addMinutes($this->client_timezone * 60);
    }
}
