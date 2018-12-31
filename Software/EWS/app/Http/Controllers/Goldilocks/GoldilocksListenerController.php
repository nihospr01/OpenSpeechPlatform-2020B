<?php

namespace App\Http\Controllers\Goldilocks;

use App\GoldilocksListener;
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
    public function destroy(GoldilocksListener $goldilocksListener)
    {
        //
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
