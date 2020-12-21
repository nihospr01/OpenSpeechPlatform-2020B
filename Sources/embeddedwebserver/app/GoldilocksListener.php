<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Model for Listener object. Functions written here can be invoked on a
 * listener object at any time.
 */
class GoldilocksListener extends Model
{
    protected $fillable = ['listener', 'pin', 'researcher_id', 'current_program_id'];

    /**
     * Returns researcher object tied to this listener in the database.
     */
    public function researcher(){
        return $this->belongsTo('App\GoldilocksResearcher', 'researcher_id', 'id');
    }

    /**
     * Returns an array of goldilocks programs tied to listener in the database.
     */
    public function programs(){
        return $this->hasMany('App\GoldilocksProgram', 'listener_id');
    }

}
