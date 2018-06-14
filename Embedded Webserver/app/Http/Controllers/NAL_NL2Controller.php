<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NAL_NL2Controller extends Controller
{

    public function getParameters(Request $request){

    	$this->validate($request, [
    		'dob' => 'required',
    		'gender' => 'required',
    		'tonal' => 'required',
    		'experience' => 'required', 
    		'num_aids' => 'required',
    		'ha_type' => 'required'
    	]);

        $audiogram = $request->input('audiogram');

    	$params_string = nalnl2_prescription((int)$request->input('dob'), (int)$request->input('gender'),
    		(int)$request->input('tonal'), (int)$request->input('experience'), (int)$request->input('num_aids'),
    		(int)$request->input('ha_type'), (int)$audiogram[0], (int)$audiogram[1], (int)$audiogram[2],
            (int)$audiogram[3], (int)$audiogram[4], (int)$audiogram[5]);

    	$params = json_decode($params_string);

    	foreach($params as &$list){
    		foreach($list as &$decimal){
    			$decimal = round($decimal, 2);
    		}
    	}

    	return json_encode($params);
    }

    public function updateParameters(Request $request){

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
                        (int)$request->input('knee_high')[0],
                        (int)$request->input('knee_high')[1],
                        (int)$request->input('knee_high')[2],
                        (int)$request->input('knee_high')[3],
                        (int)$request->input('knee_high')[4],
                        (int)$request->input('knee_high')[5]
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

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($socket, 'localhost', '8005') === true){
            $action_update_params = ["REQUEST_ACTION" => 1, "VERSION" => 2];
            socket_write($socket, json_encode($action_update_params).json_encode($values), strlen(json_encode($action_update_params).json_encode($values)));
        }
    }

}






























