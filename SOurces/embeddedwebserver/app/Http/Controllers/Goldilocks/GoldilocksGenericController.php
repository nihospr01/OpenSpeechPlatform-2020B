<?php

namespace App\Http\Controllers\Goldilocks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoldilocksGeneric;

class GoldilocksGenericController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $genericProgram = GoldilocksGeneric::first();
        return view('goldilocks.admin.generic', compact('genericProgram'));
    }

    /**
     * Update the specified resource in storage. Listener must be logged in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //get data from request
        $data = $request->json()->all();

        //find program to update using information from request
        $program = GoldilocksGeneric::first();

        //change data and save program
        $program->parameters = json_encode($data);
        $program->save();

        return json_encode(['status' => 'success']);
    }
}