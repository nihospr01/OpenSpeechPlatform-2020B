<?php

namespace App\Http\Controllers\Goldilocks;

use App\GoldilocksResearcher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoldilocksResearcherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $researchers = GoldilocksResearcher::all();
        return view('goldilocks.admin.researchers', compact('researchers'));
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
            'researcher' => 'required|unique:goldilocks_researchers|max:255'
        ]);

        $researcher = new GoldilocksResearcher;
        $researcher->researcher = $request->input('researcher');
        $researcher->save();

        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GoldilocksResearcher  $goldilocksResearcher
     * @return \Illuminate\Http\Response
     */
    public function show(GoldilocksResearcher $goldilocksResearcher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GoldilocksResearcher  $goldilocksResearcher
     * @return \Illuminate\Http\Response
     */
    public function edit(GoldilocksResearcher $goldilocksResearcher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GoldilocksResearcher  $goldilocksResearcher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GoldilocksResearcher $goldilocksResearcher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GoldilocksResearcher  $goldilocksResearcher
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoldilocksResearcher $goldilocksResearcher)
    {
        //
    }

    /**
     * Download the goldilocks_researchers table as a csv file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadResearchers(){
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=researchers.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $list = GoldilocksResearcher::all()->toArray();

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
