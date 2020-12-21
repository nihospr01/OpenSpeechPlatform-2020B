<?php

namespace App\Http\Controllers\Goldilocks;

use App\DeviceOnOffLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoldilocksListener;
use Carbon\Carbon;

class DeviceOnOffLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logs = DeviceOnOffLog::all();
        return view('goldilocks.admin.device_on_off_logs', compact('logs'));
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

        //create new log in database
        $log = DeviceOnOffLog::create([
            'on_time' => $data['on_time'],
            'off_time' => $data['off_time']
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
            ,   'Content-Disposition' => 'attachment; filename=device-on-off-logs.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        //grab form database
        $logs = DeviceOnOffLog::all();

        //alter data to show lvh as separate columns, and in the right order
        $list = [];
        foreach ($logs as $item){
            $temp = [
                'id' => $item['id'],
                'on_time' => $item['on_time'],
                'off_time' => $item['off_time']
            ];
            array_push($list, $temp);
        }

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
