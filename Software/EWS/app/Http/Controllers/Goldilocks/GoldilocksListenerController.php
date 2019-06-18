<?php

namespace App\Http\Controllers\Goldilocks;

use App\GoldilocksListener;
// use App\GoldilocksProgram;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoldilocksListenerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listeners = GoldilocksListener::all();
        return view('goldilocks.admin.listeners', compact('listeners'));

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'listener' => 'required|unique:goldilocks_listeners|max:255',
            'pin' => 'required'
        ]);

        $listener = new GoldilocksListener;
        $listener->listener = $request->input('listener');
        $listener->pin = $request->input('pin');

        $listener->save();

        /*
        Commented out because only need to prefill these parameters, not create a new program for each listener
        // create generic starter program
        $params = array(
            "targets" => [56,60,61,71,75,67],
            "ltass" => [46,60,61,71,75,67],
            "hearing_loss" => [25,30,35,45,55,85],
            "compression_ratio" => [1.4,1.4,1.4,1.4,1.4,1.4],
            "g50" => [14.285714285714285,4.285714285714285,6.285714285714285,14.285714285714285,15.285714285714285,19.285714285714285],
            "g65" => [10,0,2,10,11,15],
            "g80" => [5.714285714285715,-4.285714285714285,-2.2857142857142847,5.714285714285715,6.714285714285715,10.714285714285715],
            "multiplier_l" => [3,3,0,0,0,0],
            "multiplier_h" => [0,0,0,3,3,3],
            "g50_max" => [35,35,35,35,35,35],
            "knee_low" => [45,45,45,45,45,45],
            "mpo_band" => [110,110,110,110,110,110],
            "attack" => [5,5,5,5,5,5],
            "release" => [100,100,100,100,100,100],
            "gain" => 0,
            "l_min" => -40,
            "l_max" => 40,
            "l_step" => 1,
            "v_min" => -40,
            "v_max" => 40,
            "v_step" => 3,
            "h_min" => -40,
            "h_max" => 40,
            "h_step" => 1,
            "control_via" => 0,
            "afc" => 1,
            "sequence_num" => 3,
            "sequence_order" => 0,
            "app_behavior" => 0
        );

        $program = GoldilocksProgram::create([
            'listener_id' => $listener->id,
            'name' => "generic",
            'parameters' => json_encode($params)
        ]);

        $listener->current_program_id = $program->id;
        $listener->save();
        */

        return $this->index();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GoldilocksListener  $goldilocksListener
     * @return \Illuminate\Http\Response
     */
    public function show(GoldilocksListener $goldilocksListener)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GoldilocksListener  $goldilocksListener
     * @return \Illuminate\Http\Response
     */
    public function edit(GoldilocksListener $goldilocksListener)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GoldilocksListener  $goldilocksListener
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GoldilocksListener $goldilocksListener)
    {
        //
    }

    /**
     * Update only the current_program_id for the listener currently logged in.
     *
     * @param Request $request
     */
    public function updateCurrentProgram(Request $request){
        $data = $request->json()->all();
        $listener = GoldilocksListener::where('listener', session('listener'))->first();
        if($listener){
            $listener->current_program_id = $data[0];
            $listener->save();
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'error']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GoldilocksListener  $goldilocksListener
     * @return \Illuminate\Http\Response
     */
    public function destroy($goldilocksListener)
    {
        GoldilocksListener::destroy($goldilocksListener);

        return redirect('goldilocks/admin/listeners');
    }

    /**
     * Download the goldilocks_listeners table as a csv file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadListeners(){
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=listeners.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $list = GoldilocksListener::all()->toArray();

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
