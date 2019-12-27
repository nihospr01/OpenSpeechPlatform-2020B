<?php

namespace App\Http\Controllers\Goldilocks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoldilocksListener;
use App\GoldilocksResearcher;
use App\GoldilocksProgram;
use App\GoldilocksGeneric;

class GoldilocksController extends Controller
{
    /**
     * Show index page for Goldilocks.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('goldilocks.index');
    }

    /**
     * Show index page for admin functions.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminIndex(){
        return view('goldilocks.admin.index');
    }

    /**
     * Show index page for viewing or downloading logs
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function logIndex(){
        return view('goldilocks.logs.index');
    }

    /**
     * Shows researcher login page. If a researcher has already logged in, will auto-fill with the most recent
     * researcher to speed up the process.
     *
     * GET: '/researcher-login'
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function researcherLogin(){
        $curResearcher = NULL;
        if(session()->get('researcher', 'NULL') != 'NULL'){
            $curResearcher = GoldilocksResearcher::where('researcher', session('researcher'))->firstOrFail();
        }
        $researchers = GoldilocksResearcher::all();
        $listeners = GoldilocksListener::all();
        return view ('goldilocks.researcher-login', compact('listeners', 'curResearcher', 'researchers'));
    }

    /**
     * Takes researcher and listener input and attempts to login to the researcher page. Redirecting with errors if
     * an error occurs.
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function attemptLogin(Request $request){

        //validate that incoming request contains researcher and listener fields
        if($request->has('researcher', 'listener')){

            //Check to see if researcher exists
            $researcher = GoldilocksResearcher::where('researcher', $request->input('researcher'))->first();
            if(!$researcher){
                return redirect('/goldilocks/researcher/login')->withErrors(['Researcher  "'.$request->input('researcher').'" not found.']);
            }

            //update listener to reflect most recent researcher interaction
            $listener = GoldilocksListener::where('listener', $request->input('listener'))->first();
            $listener->researcher_id = $researcher->id;
            $listener->save();

            //store in the global session listener and researcher who have logged in
            session(['listener' => $listener->listener]);
            session(['researcher' => $researcher->researcher]);

            //go to goldilocks researcher page
            return redirect('/goldilocks/researcher');
        }

        //Break if no input has been passed through
        else{
            return redirect('/goldilocks/researcher/login')->withErrors(['Please input Researcher ID']);
        }
    }

    /**
     * Gathers requirements for functioning researcher page from database and loads researcher page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function researcherPage(){
        //check to make sure researcher has logged in properly
        if(session()->get('researcher', 'NULL') != 'NULL' && session()->get('listener', 'NULL') != 'NULL'){
            //grab listener information
            $listener = GoldilocksListener::where('listener', session('listener'))->firstOrFail();

            //grab listener programs
            $programs = $listener->programs;

            //create $parameters[$program->id] object for easier access in web page
            $parameters = [];
            foreach($programs as $p){
                $parameters[$p->id] = json_decode($p->parameters);
            }

            //encode as a JSON string
            $parameters = json_encode($parameters);

            //grab researcher from database
            $researcher = GoldilocksResearcher::where('researcher', session('researcher'))->firstOrFail();

            $genericProgram = GoldilocksGeneric::first();

            //redirect to researcher page with required parameters
            return view('goldilocks.researcher', compact('listener', 'programs', 'researcher', 'parameters', 'genericProgram'));

        }
        //take user to login page if they are not logged in
        else{
            return redirect('/goldilocks/researcher/login');
        }
    }

    /**
     * Transition from researcher-goldilocks to user-goldilocks with the researcher parameters in place.
     * program_id will never be null because it is required to be set before leaving the researcher page
     *
     * POST: '/researcher-goldilocks'

     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function transitionToGoldilocksApp(Request $request){
        $parameters = $request->input('data');
        $listener = GoldilocksListener::where('listener', session('listener'))->firstOrFail();
        $program_id = $listener->current_program_id;

        // get next program name
        $program = GoldilocksProgram::where('id', $program_id)->firstOrFail();
        $next_name = $program->getOriginal('name') . " " . ($program->name_rank + 1);

        return view('goldilocks.listener', compact('listener','parameters', 'program_id', 'next_name'));

    }

    /**
     * Load page for logging into self adjustment in case session was cleared.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listenerLoginPage(){
        $listeners = GoldilocksListener::all();
        return view('goldilocks.listener-login', compact('listeners'));
    }

    /**
     * Store the listener as a session variable and launch the self adjustment page.
     *
     * @param Request $request
     * @return $this|View
     */
    public function listenerLogin(Request $request){
        //validate that incoming request contains listener field
        if($request->has('listener')){
            $listener = GoldilocksListener::where('listener', $request->input('listener'))->firstOrFail();

            //store in the global session listener and researcher who have logged in
            session(['listener' => $listener->listener]);
            if($listener->researcher_id){
                $researcher = $listener->researcher;
                session(['researcher' => $researcher->researcher]);
            }

            //go to goldilocks listener page
            return $this->selfAdjustment();
        }

        //Break if no input has been passed through
        else{
            return redirect('/goldilocks/listener/login')->withErrors(['Something went wrong']);
        }

    }

    /**
     * Logs out listener and flushes session.
     *
     * @param Request $request
     * @return $this|View
     */
    public function listenerLogout(Request $request){
        $request->session()->flush();
        return redirect('/goldilocks/listener/login');
    }

    // get next rank until it doesn't exist
    private function getNextRankName($name, $rank, $listener_id) {
        while (GoldilocksProgram::where(['listener_id' => $listener_id, 'name' => $name, 'name_rank' => $rank])->exists()) {
            $rank = $rank + 1;
        }

        return $name . "-" . $rank;

    }

    /**
     * Loads listener goldilocks page for self adjustment.
     *
     * @return View
     */
    public function selfAdjustment($program_id = null){
        $listener = GoldilocksListener::where('listener', session('listener'))->first();
        $parameters = null;
        $next_name = "";
        if($program_id){
            $program = GoldilocksProgram::where('id', $program_id)->firstOrFail();
            $parameters = $program->parameters;
            $next_name = self::getNextRankName($program->getOriginal('name'), $program->name_rank + 1, $program->listener_id);
        }


        //get most recent program for use if it exists
        if($program_id == null && $listener->current_program_id != null) {
            $program_id = $listener->current_program_id;
            $program = GoldilocksProgram::where('id', $program_id)->first();
            $parameters = $program->parameters;

            $next_rank = $program->name_rank + 1;

            $next_name = self::getNextRankName($program->getOriginal('name'), $program->name_rank + 1, $program->listener_id);

            //check to see if it's listener only
            $data = json_decode($parameters);
            if($data->app_behavior == 1){
                return view('goldilocks.listener', compact('listener', 'parameters', 'program_id', 'next_name'));
            }
            else{
                return redirect('/goldilocks/listener/programs');
            }
        }

        return view('goldilocks.listener', compact('listener', 'parameters', 'program_id', 'next_name'));
    }

    /**
     * Loads page with all listener programs.
     *
     * @return View
     */
    public function programsPage(){
        $listener = GoldilocksListener::where('listener', session('listener'))->firstOrFail();

        //if user is supposed to have volume only, redirect
        if($listener->current_program_id != null){
            $program_id = $listener->current_program_id;
            $program = GoldilocksProgram::where('id', $program_id)->first();
            if ($program == null) {
                // reassign another program that belongs to the same listener
                $program = GoldilocksProgram::where('listener_id', $listener->id)->firstOrFail();
                $listener->current_program_id = $program->id;
                $listener->save();
            }

            $parameters = $program->parameters;
            $data = json_decode($parameters);
            if($data->app_behavior == 1){
                return redirect('/goldilocks/listener');
            }
        }



        $programs = $listener->programs;

        $parameters = [];
        foreach($programs as $p){
            $parameters[$p->id] = json_decode($p->parameters);
        }

        $parameters = json_encode($parameters);

        return view('goldilocks.programs', compact('listener', 'programs', 'parameters'));

    }

    public function modifyProgram(Request $request){
//        $listener = GoldilocksListener::where('listener', session('listener'))->firstOrFail();
        $program_id = $request->input('program_id');
//        $program = GoldilocksProgram::where('id', $program_id)->firstOrFail();
//        $parameters = $program->parameters;

        return $this->selfAdjustment($program_id);
    }
}
