<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Calibration;

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
        //grab all data sent in request as a JSON string
        $data = $request->json()->all();
      
        // apply calibration if exists
        $defaultCalibration = Calibration::where('default', 1)->first();  
        if ($defaultCalibration) {
            $corrections = $defaultCalibration->getAPIString();

            $channels = ['left', 'right'];
            $subElements = ['mpo_band'];
            $mulElements = ['attack', 'release'];

            foreach($channels as $channel) {
                for ($i = 0; $i < 6; $i++) {
                    // break out of loop for searchex
                    if(!isset($data['data'][$channel]['g50']) || !isset($data['data'][$channel]['mpo_band'])){
                        break;
                    }
                    /******* G50 and G80 *******/
                    // get desired g65 and cr from g50 and g80 passed
                    $g50 = $data['data'][$channel]['g50'][$i];
                    $g80 = $data['data'][$channel]['g80'][$i];
                    $slope = ($g80 - $g50)/30;
                    $g65 = round($g50 + $slope * 15, 1);
                    $cr = round( (1 / (1 + $slope)) * 100 ) / 100;

                    // calculate corrected g65 -> recalculate corrected g50 and g80
                    $cg65 = $g65 - $corrections[$channel]['g65'][$i];
                    $slope = (1 - $cr) / $cr;
                    $cg50 = round($cg65 - 15 * $slope, 1);
                    $cg80 = round($cg65 + 15 * $slope, 1);

                    $data['data'][$channel]['g50'][$i] = $cg50;
                    $data['data'][$channel]['g80'][$i] = $cg80;

                    /******* mpo_band and knee_low *******/
                    foreach ($subElements as $se) {
                        $data['data'][$channel][$se][$i] -= $corrections[$channel][$se][$i];
                    }
                    $data['data'][$channel]['spl_calibration'][$i] = 100 + $corrections[$channel]['knee_low'][$i];

                    /******* attack and release *******/
                    foreach ($mulElements as $me) {
                        // has no calibration value
                        if(!$corrections[$channel][$me][$i]){
                            continue;
                        }
                        $data['data'][$channel][$me][$i] /= $corrections[$channel][$me][$i];
                    }
                }
            } 
        }

        $addr = gethostbyname("127.0.0.1");
        $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

        //throw error if can not connect to MHA
        if ($client === false) {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }

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
     * Sends a JSON string of the uncalibrated parameters to the MHA. 
     * String is found in the body of the request object.
     * POST: '/api/paramsUncalibrated'
     *
     * @param Request $request The POST request data.
     *
     * @return string
     */
    public function updateParametersUncalibrated(Request $request){
        //grab all data sent in request as a JSON string
        $data = $request->json()->all();

        $addr = gethostbyname("127.0.0.1");
        $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);
    
        //throw error if can not connect to MHA
        if ($client === false) {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }

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
        //initialize get request data using random user ID 10
        $test_data = [
            "user_id" => 10,
            "method" => "get",
            "request_action" => 1,
            "data" => []
        ];

        $addr = gethostbyname("127.0.0.1");
        $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

        //throw error if can not connect to MHA
        if ($client === false) {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }

        //open connection and write to MHA
        fwrite($client, json_encode($test_data));

        //read from MHA
        $response = stream_get_contents($client, -1, -1);
        $response = json_decode($response, true);
        //close connection
        fclose($client);

        // apply calibration if exists
        $defaultCalibration = Calibration::where('default', 1)->first();  
        if ($defaultCalibration) {
            $corrections = $defaultCalibration->getAPIString();
            $channels = ['left', 'right'];
            $subElements = ['mpo_band'];
            $mulElements = ['attack', 'release'];

            foreach($channels as $channel) {
                for ($i = 0; $i < 6; $i++) {
                    /******* G50 and G80 *******/
                    // get corrected g65 and cr from g50 and g80 passed
                    $g50 = $response[$channel]['g50'][$i];
                    $g80 = $response[$channel]['g80'][$i];
                    $slope = ($g80 - $g50)/30;
                    $g65 = round($g50 + $slope * 15, 1);
                    $cr = round( (1 / (1 + $slope)) * 100 ) / 100;

                    // calculate desired g65 -> recalculate corrected g50 and g80
                    $dg65 = $g65 + $corrections[$channel]['g65'][$i];
                    $slope = (1 - $cr) / $cr;
                    $dg50 = round($dg65 - 15 * $slope, 1);
                    $dg80 = round($dg65 + 15 * $slope, 1);

                    $response[$channel]['g50'][$i] = $dg50;
                    $response[$channel]['g80'][$i] = $dg80;

                    /******* mpo_band and knee_low *******/
                    foreach ($subElements as $se) {
                        $response[$channel][$se][$i] += $corrections[$channel][$se][$i];
                    }

                    /******* attack and release *******/
                    foreach ($mulElements as $me) {
                        $response[$channel][$me][$i] *= $corrections[$channel][$me][$i];
                    }
                }
            } 
        }

        //send back response from MHA
        return json_encode($response);
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
