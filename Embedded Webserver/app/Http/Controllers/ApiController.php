<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function updateParams(Request $request){
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
                        (int)$request->input('kneelow')[0],
                        (int)$request->input('kneelow')[1],
                        (int)$request->input('kneelow')[2],
                        (int)$request->input('kneelow')[3],
                        (int)$request->input('kneelow')[4],
                        (int)$request->input('kneelow')[5]
                        ],
            "mpoLimit" => [  
                        (int)$request->input('mpoLimit')[0],
                        (int)$request->input('mpoLimit')[1],
                        (int)$request->input('mpoLimit')[2],
                        (int)$request->input('mpoLimit')[3],
                        (int)$request->input('mpoLimit')[4],
                        (int)$request->input('mpoLimit')[5]
                        ],
            "attackTime" => [  
                        (int)$request->input('attackTime')[0],
                        (int)$request->input('attackTime')[1],
                        (int)$request->input('attackTime')[2],
                        (int)$request->input('attackTime')[3],
                        (int)$request->input('attackTime')[4],
                        (int)$request->input('attackTime')[5]
                        ],
            "releaseTime" => [  
                        (int)$request->input('releaseTime')[0],
                        (int)$request->input('releaseTime')[1],
                        (int)$request->input('releaseTime')[2],
                        (int)$request->input('releaseTime')[3],
                        (int)$request->input('releaseTime')[4],
                        (int)$request->input('releaseTime')[5]
                        ]
        ];

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($socket, 'localhost', '8005') === true){
            $action_update_params = ["REQUEST_ACTION" => 1, "VERSION" => 2];
            socket_write($socket, json_encode($action_update_params).json_encode($values), strlen(json_encode($action_update_params).json_encode($values)));
        }
    }

    public function playSound(Request $request){

    	$data = $request->json()->all();

    	switch($data["Actual_answer"]){
    		case "/../../../resources/sounds/01/01.wav": 
    			$data["Word_set"] = __DIR__."/../../../resources/sounds/01/";
    			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/01/01.wav";
    			break;
    		case "/../../../resources/sounds/02/02.wav": 
    			$data["Word_set"] = __DIR__."/../../../resources/sounds/02/";
    			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/02/02.wav";
    			break;
    		case "/../../../resources/sounds/03/03.wav": 
    			$data["Word_set"] = __DIR__."/../../../resources/sounds/03/";
    			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/03/03.wav";
    			break;
    		case "/../../../resources/sounds/04/04.wav": 
    			$data["Word_set"] = __DIR__."/../../../resources/sounds/04/";
    			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/04/04.wav";
    			break;
    		//in case nothing is there, use the first file to avoid errors
    		default:
    			$data["Word_set"] = __DIR__.$data["Word_set"];
    			$data["Actual_answer"] = __DIR__.$data["Actual_answer"];
    			break;
    	}

    	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($socket, 'localhost', '8005') === true){
            $action_update_params = ["REQUEST_ACTION" => 8, "VERSION" => 2];
            socket_write($socket, json_encode($action_update_params).json_encode($data,JSON_UNESCAPED_SLASHES), strlen(json_encode($action_update_params).json_encode($data,JSON_UNESCAPED_SLASHES)));

        }
    }


    public function connect(Request $request){
        $data = $request->json()->all();

        //break if it's missing data
        if(!$data["URI"]){
            return json_encode(["error" => "missing tester or participant in json request"]);
        }
        else if(strlen($data["URI"]) > 9){
            return json_encode(["error" => "URI length must be 9 or less characters"]);
        }
        else{
            $uri = ["URI" => $data["URI"]];
            $ls = ["LS" => strlen($uri["URI"])];

            // return json_encode([$data, $uri, $ls]);

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if(socket_connect($socket, 'localhost', '8005') === true){
                $action_connect = ["REQUEST_ACTION" => 3, "VERSION" => 2];
                $str_to_write = json_encode($action_connect).json_encode($ls).json_encode($uri);
                $str_len = strlen(json_encode($action_connect).json_encode($ls).json_encode($uri));
                socket_write($socket, $str_to_write, $str_len);
            }
        }
    }

    public function disconnect(){
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if(socket_connect($socket, 'localhost', '8005') === true){
            $action_disconnect = ["REQUEST_ACTION" => 6, "VERSION" => 2];
            socket_write($socket, json_encode($action_disconnect), strlen(json_encode($action_disconnect)));

        }
    }
}
