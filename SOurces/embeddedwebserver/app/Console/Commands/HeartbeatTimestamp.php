<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;
use App\DeviceOnOffLog;

class HeartbeatTimestamp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heartbeat:write';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes current timestamp as a heartbeat to storage/endtime.log and ' . 
                             'system start time to storage/starttime.log file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startfile = "starttime.log";
        $endfile = "endtime.log";
        $cur_start_time = trim(shell_exec("uptime -s"));

        // startup timestamp exists
        if (Storage::disk('local')->exists($startfile)) {
            $prev_start_time = Storage::get($startfile);

            // start time changed -- new start up
            if ($prev_start_time != $cur_start_time) {
                $prev_end_time = Storage::get($endfile);
                DeviceOnOffLog::create([
                    'on_time' => Carbon::parse($prev_start_time),
                    'off_time' => Carbon::parse($prev_end_time)
                ]);
            }
        }

        // heartbeat writes
        Storage::put($startfile, $cur_start_time);
        Storage::put($endfile, Carbon::now("PST")->toDateTimeString());
    }
}
