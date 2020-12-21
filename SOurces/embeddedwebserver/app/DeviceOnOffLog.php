<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceOnOffLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'on_time',
        'off_time'
    ];

    protected $dates = [
        'on_time',
        'off_time'
    ];
}
