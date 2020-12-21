<?php

namespace App\Http\Controllers\Goldilocks;

use App\GoldilocksProgram;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoldilocksListener;

class GoldilocksProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $programs = GoldilocksProgram::all();
       
        return view('goldilocks.admin.programs', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage. Listener must be logged in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //get listener that's logged in
        $listener = GoldilocksListener::where('listener', session('listener'))->first();

        //get data from request
        $data = $request->json()->all();

        // split name and rank
        $program_name = $data[0];
        $rank = 1;
        $program_array = explode("-", $program_name);

        if (count($program_array) > 1) {
            $rank = (int)$program_array[count($program_array) - 1];
            $program_name = implode("-",  array_slice($program_array, 0, -1));
        }


        if(GoldilocksProgram::where(['listener_id' => $listener->id, 'name' => $program_name, 'name_rank' => $rank])->exists()){
            return json_encode(['status' => 'failure']);
        }

        //create new program in database
        $program = GoldilocksProgram::create([
            'listener_id' => $listener->id,
            'name' => $program_name,
            'name_rank' => $rank,
            'parameters' => json_encode($data[1]),
            'parametersTwoChannels' => json_encode($data[2])
        ]);

        //set this program as the current program for the listener if it wasn't already set
        $listener->current_program_id = $program->id;
        $listener->save();

        return json_encode(['status' => 'success', 'id' => $program->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GoldilocksProgram  $goldilocksProgram
     * @return \Illuminate\Http\Response
     */
    public function show(GoldilocksProgram $goldilocksProgram)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GoldilocksProgram  $goldilocksProgram
     * @return \Illuminate\Http\Response
     */
    public function edit(GoldilocksProgram $goldilocksProgram)
    {
        //
    }

    /**
     * Update the specified resource in storage. Listener must be logged in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //get listener
        $listener = GoldilocksListener::where('listener', session('listener'))->first();

        //get data from request
        $data = $request->json()->all();

        //find program to update using information from request
        $program = GoldilocksProgram::where('id', $data[0])->first();

        //change data and save program
        $program->parameters = json_encode($data[1]);
        $program->parametersTwoChannels = json_encode($data[2]);
        $program->save();

        //set this program as the current program for the listener if it wasn't already set
        $listener->current_program_id = $program->id;
        $listener->save();

        return json_encode(['status' => 'success']);
    }

    /**
     * Remove the specified resource from storage, and redirect to admin index.
     *
     * @param  \App\GoldilocksProgram  $goldilocksProgram
     * @return \Illuminate\Http\Response
     */
    public function destroy($goldilocksProgram)
    {
        GoldilocksProgram::destroy($goldilocksProgram);

        return redirect('goldilocks/admin/programs');
    }

    /**
     * Remove the specified resource from storage, and redirect to listener programs.
     *
     * @param  \App\GoldilocksProgram  $goldilocksProgram
     * @return \Illuminate\Http\Response
     */
    public function delete($goldilocksProgram)
    {
        GoldilocksProgram::destroy($goldilocksProgram);

        return redirect('goldilocks/listener/programs');
    }

    /**
     * Transmits the program to the RTMHA. Listener must be logged in.
     */
    public function transmit(Request $request){

        //get listener
        $listener = GoldilocksListener::where('listener', session('listener'))->first();

        //get the data from the incoming request
        $data = $request->json()->all();

        //find the program in the database
        $program = GoldilocksProgram::where('id', $data["program_id"])->first();

        //begin transmitting
        $addr = gethostbyname("127.0.0.1");
        $client = stream_socket_client("tcp://$addr:8001", $errno, $errorMessage);

        //throw error if can not connect to MHA
        if ($client === false) {
            throw new UnexpectedValueException("Failed to connect: $errorMessage");
        }

        $data = $program->getAPIString();
  
        $payload = [
            'user_id' => $listener->id,
            'method' => 'set',
            'request_action' => 1,
            'data' => ['left' => $data['left'], 'right' => $data['right']]
        ];

        //open connection and write to MHA
        fwrite($client, json_encode($payload));
        //read from MHA
        $response = stream_get_contents($client, -1, -1);
        //close connection
        fclose($client);

        // set this program as the current program for the listener after transmit
        $listener->current_program_id = $program->id;
        $listener->save();

        //send back response from MHA
        return $response;
    }

    /**
     * Download the goldilocks_programs table as a csv file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadPrograms(){
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=programs.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $list = GoldilocksProgram::all()->toArray();

        //only continue if there are elements in the array
        if(count($list) > 0) {
            # add headers for each column in the CSV download
            array_unshift($list, array_keys($list[0]));
        }
        else{
            $list[0] = ['Error', 'Database shows no data for this table.'];
        }

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }


}
