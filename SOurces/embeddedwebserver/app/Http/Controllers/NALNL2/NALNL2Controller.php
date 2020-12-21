<?php

namespace App\Http\Controllers\NALNL2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NALNL2Controller extends Controller
{
    /**
     * Loads the view for nalnl2.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loadPage(){
        return view('NALNL2.main');
    }


    /**
     * Gets NALNL2 user parameters and logs to database.
     * POST: '/nalnl2'
     *
     * @param  Request
     * @return string log info
     */
    public function getParameters(Request $request){

        //Validate that incoming request has all required information
        $this->validate($request, [
            'dob' => 'required',
            'gender' => 'required',
            'tonal' => 'required',
            'experience' => 'required',
            'num_aids' => 'required',
            'ha_type' => 'required',
            'audiogram' => 'required'
        ]);

        //transform audiogram from strings to ints
        $audiogram = array_map(function($value){ return (int)$value; }, $request->input('audiogram'));

        //Gather incoming information into one array
        $data = [ 'dob' => (int)$request->input('dob'),
            'gender' => (int)$request->input('gender'),
            'tonal' => (int)$request->input('tonal'),
            'experience' => (int)$request->input('experience'),
            'num_aids' => (int)$request->input('num_aids'),
            'ha_type' => (int)$request->input('ha_type'),
            'audiogram' => $audiogram
        ];

        //Create a log in database to signal an incoming NALNL2 user-parameter request
        $inputLog = Log::create([
            'request_action' => 10,
            'data' => json_encode($data),
            'status' => 0
        ]);
        //Create a log in database where OSP will fill in correct parameters
        $outputLog = Log::create([
            'request_action' => 9,
            'data' => "",
            'status' => 0
        ]);

        //Connect to EWS open socket to tell OSP about update
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($socket, 'localhost', '8001') === true){
            $action_update_params = ["REQUEST_ACTION" => 10, "ID" => $inputLog->id];
            $json_update_params = json_encode($action_update_params);
            socket_write($socket, $json_update_params, strlen($json_update_params));
        }

        //Periodically check database to see if OSP has responded
        //Currently set to check every 0.25 seconds for 5 seconds
        $whileLog = 0;
        $start = microtime(true);
        while(microtime(true) - $start < 5){
            $whileLog = Log::where("id", $outputLog->id)->first();
            if($whileLog->status != 0){
                break 1;
            }
            //sleep for a quarter of a second
            usleep(250000);
        }

        //Return log info along with status to see if OSP successfully placed data
        return (json_encode(["log_id" => $whileLog->id, "status" => $whileLog->status, "data" => $whileLog->data]));
    }

    /**
     * Updates NALNL2 parameters of OSP and logs to database.
     * POST: '/nalnl2update'
     *
     * @param  Request
     * @return string log info
     */
    public function updateParameters(Request $request){

        //Gather data from incoming request and store it into one array
        $values = [
            "noOp" => 0,
            "afc" => 1,
            "feedback" => 1,
            "rear" => 1,
            "g50" => [
                (int)$request->input('g50')[0],
                (int)$request->input('g50')[1],
                (int)$request->input('g50')[2],
                (int)$request->input('g50')[3],
                (int)$request->input('g50')[4],
                (int)$request->input('g50')[5]
            ],
            "g80" => [
                (int)$request->input('g80')[0],
                (int)$request->input('g80')[1],
                (int)$request->input('g80')[2],
                (int)$request->input('g80')[3],
                (int)$request->input('g80')[4],
                (int)$request->input('g80')[5]
            ],
            "kneelow" => [
                (int)$request->input('knee_low')[0],
                (int)$request->input('knee_low')[1],
                (int)$request->input('knee_low')[2],
                (int)$request->input('knee_low')[3],
                (int)$request->input('knee_low')[4],
                (int)$request->input('knee_low')[5]
            ],
            "mpoLimit" => [
                (int)$request->input('mpo_band')[0],
                (int)$request->input('mpo_band')[1],
                (int)$request->input('mpo_band')[2],
                (int)$request->input('mpo_band')[3],
                (int)$request->input('mpo_band')[4],
                (int)$request->input('mpo_band')[5]
            ],
            "attackTime" => [
                (int)$request->input('attack')[0],
                (int)$request->input('attack')[1],
                (int)$request->input('attack')[2],
                (int)$request->input('attack')[3],
                (int)$request->input('attack')[4],
                (int)$request->input('attack')[5]
            ],
            "releaseTime" => [
                (int)$request->input('release')[0],
                (int)$request->input('release')[1],
                (int)$request->input('release')[2],
                (int)$request->input('release')[3],
                (int)$request->input('release')[4],
                (int)$request->input('release')[5]
            ]
        ];

        //Create log for OSP to read from database
        $log = Log::create([
            'request_action' => 1,
            'data' => json_encode($values),
            'status' => 0
        ]);

        //Connect to EWS open socket to tell OSP about update
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($socket, 'localhost', '8001') === true){
            $action_update_params = ["REQUEST_ACTION" => 1, "ID" => $log->id];
            $json_update_params = json_encode($action_update_params);
            socket_write($socket, $json_update_params, strlen($json_update_params));
        }

        //Periodically check database to see if OSP has responded
        //Currently set to check every 0.25 seconds for 5 seconds
        $whileLog = 0;
        $start = microtime(true);
        while(microtime(true) - $start < 5){
            $whileLog = Log::where("id", $log->id)->first();
            if($whileLog->status != 0){
                break 1;
            }
            //sleep for a quarter of a second
            usleep(250000);
        }

        //Return log info along with status to see if OSP successfully placed data
        return (json_encode(["log_id" => $whileLog->id, "status" => $whileLog->status]));

    }
}
