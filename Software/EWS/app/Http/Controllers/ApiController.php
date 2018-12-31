<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Log;

/**
 * Controller for accepting REST API requests. This controller holds all 
 * REST API function calls. These calls should be universal, and not tied to any
 * one app. This is the controller in in charge of communicating with the MHA.
 */

class ApiController extends Controller
{
    /**
     * Sends a JSON string to the MHA. String is found in the body of the 
     * request object
     * POST: '/api/params'
     *
     * @param Request $request The POST request data.
     *
     * @return string
     */
    public function updateParameters(Request $request){
        $addr = gethostbyname("127.0.0.1");

        $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

        //throw error if can not connect to MHA
        if ($client === false) {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }

        //grab all data sent in request as a JSON string
        $data = $request->json()->all();

        //open connection and write to MHA
        fwrite($client, json_encode($data));
        //read from MHA
        $response = stream_get_contents($client, -1, -1);
        //close connection
        fclose($client);

        //send back response from MHA
        return $response;
    }


    /**
     * Gets the current parameters of the MHA via web socket.
     * GET: '/api/getParams'
     *
     * @return string
     */
    public function getParameters(){

        $addr = gethostbyname("127.0.0.1");

        $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

        //throw error if can not connect to MHA
        if ($client === false) {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }

        //initialize get request data using random user ID 10
        $test_data = [
            "user_id" => 10,
            "method" => "get",
            "request_action" => 1,
            "data" => []

        ];

        //open connection and write to MHA
        fwrite($client, json_encode($test_data));
        //read from MHA
        $response = stream_get_contents($client, -1, -1);
        //close connection
        fclose($client);

        //send back response from MHA
        return $response;
    }

    // /**
    //  * Play pre-determined sound files in MHA controlled by web client via socket.
    //  * POST: 'api/4afc'
    //  *
    //  * @param  Request $request The POST request data
    //  */
    // public function playSound(Request $request){

    // 	$data = $request->json()->all();

    // 	switch($data["Actual_answer"]){
    // 		case "/../../../resources/sounds/01/01.wav":
    // 			$data["Word_set"] = __DIR__."/../../../resources/sounds/01/";
    // 			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/01/01.wav";
    // 			break;
    // 		case "/../../../resources/sounds/02/02.wav":
    // 			$data["Word_set"] = __DIR__."/../../../resources/sounds/02/";
    // 			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/02/02.wav";
    // 			break;
    // 		case "/../../../resources/sounds/03/03.wav":
    // 			$data["Word_set"] = __DIR__."/../../../resources/sounds/03/";
    // 			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/03/03.wav";
    // 			break;
    // 		case "/../../../resources/sounds/04/04.wav":
    // 			$data["Word_set"] = __DIR__."/../../../resources/sounds/04/";
    // 			$data["Actual_answer"] = __DIR__."/../../../resources/sounds/04/04.wav";
    // 			break;
    // 		//in case nothing is there, use the first file to avoid errors
    // 		default:
    // 			$data["Word_set"] = __DIR__.$data["Word_set"];
    // 			$data["Actual_answer"] = __DIR__.$data["Actual_answer"];
    // 			break;
    // 	}

    // 	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    //     if(socket_connect($socket, 'localhost', '8005') === true){
    //         $action_update_params = ["REQUEST_ACTION" => 8, "VERSION" => 2];
    //         $json_update_params = json_encode($action_update_params).json_encode($data,JSON_UNESCAPED_SLASHES);
    //         socket_write($socket, $json_update_params, strlen($json_update_params));

    //     }
    // }

    // /**
    //  * Updates noise of MHA via web socket.
    //  * POST: '/api/noise'
    //  *
    //  * @param  Request $request The POST request data
    //  */
    // public function updateNoise(Request $request){
    //     $data = $request->json()->all();

    //     $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    //     if(socket_connect($socket, 'localhost', '8005') === true){
    //         $action_update_params = ["REQUEST_ACTION" => 1, "VERSION" => 2];
    //         $json_update_params = json_encode($action_update_params).json_encode($data,JSON_UNESCAPED_SLASHES);
    //         socket_write($socket, $json_update_params, strlen($json_update_params));
    //     }
    // }

    // /**
    //  * Updates feedback of MHA via web socket.
    //  * POST: '/api/feedback'
    //  *
    //  * @param  Request $request The POST request data
    //  */
    // public function updateFeedback(Request $request){
    //     $data = $request->json()->all();

    //     $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    //     if(socket_connect($socket, 'localhost', '8005') === true){
    //         $action_update_params = ["REQUEST_ACTION" => 1, "VERSION" => 2];
    //         $json_update_params = json_encode($action_update_params).json_encode($data,JSON_UNESCAPED_SLASHES);
    //         socket_write($socket, $json_update_params, strlen($json_update_params));
    //     }
    // }

}
