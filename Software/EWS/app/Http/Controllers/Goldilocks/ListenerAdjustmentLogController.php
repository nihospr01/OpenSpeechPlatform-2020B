<?php

namespace App\Http\Controllers\Goldilocks;

use App\ListenerAdjustmentLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoldilocksListener;
use Carbon\Carbon;

class ListenerAdjustmentLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logs = ListenerAdjustmentLog::all();
        return view('goldilocks.admin.adjustment_logs', compact('logs'));
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
        //get data from request
        $data = $request->json()->all();

        //get listener currently logged in
        $listener = GoldilocksListener::where('listener', session('listener'))->firstOrFail();

        //create new log in database
        $log = ListenerAdjustmentLog::create([
            'listener_id' => $listener->id,
            'researcher_id' => $listener->researcher_id,
            'start_program_id' => $data['start_program_id'],
            'end_program_id' => $data['end_program_id'],
            'final_lvh' => json_encode($data['final_lvh']),
            'steps' => $data['steps'],
            'seconds_elapsed' => round($data['time_ms'] / 1000.0),
            'change_string' => $data['changes'],
            'starting_g65' => json_encode($data['starting_g65']),
            'ending_g65' => json_encode($data['ending_g65']),
            'compression_ratio' => json_encode($data['cr']),
            'l_multipliers' => json_encode($data['lmul']),
            'h_multipliers' => json_encode($data['hmul']),
            'client_finish' => Carbon::parse($data['timestamp']),
            'client_timezone' => $data['timezone']
        ]);

        return json_encode(['status' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GoldilocksLog  $goldilocksLog
     * @return \Illuminate\Http\Response
     */
    public function show(GoldilocksLog $goldilocksLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GoldilocksLog  $goldilocksLog
     * @return \Illuminate\Http\Response
     */
    public function edit(GoldilocksLog $goldilocksLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GoldilocksLog  $goldilocksLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GoldilocksLog $goldilocksLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GoldilocksLog  $goldilocksLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoldilocksLog $goldilocksLog)
    {
        //
    }

    /**
     * Download the goldilocks_logs table as a csv file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadLogs(){
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=adjustment-logs.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        //grab form database
        $logs = ListenerAdjustmentLog::all();

        //alter data to show lvh as separate columns, and in the right order
        $list = [];
        foreach ($logs as $item){
            $values = json_decode($item['final_lvh']);

            // set default values for listener and researcher in case they're deleted
            $listener_name = "deleted";
            if (!empty($item->listener)) {
                $listener_name = $item->listener->listener;
            }

            $researcher_name = "deleted";
            if (!empty($item->researcher)) {
                $researcher_name = $item->researcher->researcher;
            }

            $start_program_name = "deleted";
            if (!empty($item->startProgram)) {
                $start_program_name = $item->startProgram->name;
            }

            $end_program_name = "deleted";
            if (!empty($item->endProgram)) {
                $end_program_name = $item->endProgram->name;
            }

            $temp = [
                'id' => $item['id'],
                'timestamp' => $item['client_finish_local'],
                'timezone' => $item['client_timezone'],
                'listener' => $listener_name,
                'researcher' => $researcher_name,
                'starting_program' => $start_program_name,
                'ending_program' => $end_program_name,
                'final_l' => $values->l_value,
                'final_v' => $values->v_value,
                'final_h' => $values->h_value,
                'steps' => $item['steps'],
                'seconds_elapsed' => $item['seconds_elapsed'],
                'changes' => $item['change_string'],
                'starting_g65' => $item['starting_g65'],
                'compression_ratio' => $item['compression_ratio'],
                'low_multipliers' => $item['l_multipliers'],
                'high_multipliers' => $item['h_multipliers'],
                'ending_g65' => $item['ending_g65'],
            ];
            array_push($list, $temp);
        }

        //dd($list);
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
