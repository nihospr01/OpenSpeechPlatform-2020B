<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    /**
     * Updates the parameters of the MHA as modified in the web client via web socket.
     * POST: '/api/params'
     *
     * @param Request $request The POST request data
     *
     * @return string
     */
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
        if(socket_connect($socket, 'localhost', '8001') === true){
            $action_update_params = ["REQUEST_ACTION" => 1, "VERSION" => 2];
            $json_update_params = json_encode($action_update_params).json_encode($values);
            socket_write($socket, $json_update_params, strlen($json_update_params));
        }
    }

    public function researcherPage(){
        return view('researcherpage');
    }

    public function researcherAmplificationPage(){
        return view('researcher.amplification');
    }

    public function researcherFeedbackPage(){
        return view('researcher.feedback');
    }

    public function researcherNoisePage(){
        return view('researcher.noise');
    }

    public function welcomePage(){
        return view('welcome');
    }

    public function ansi(){
        return view('ansi');
    }

    public function load4AFC(){
        return view('4afc');
    }
}
