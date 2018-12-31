<?php

namespace App\Http\Controllers\Goldilocks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoldilocksListener;
use App\GoldilocksResearcher;
use App\GoldilocksProgram;

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
        $researcher = NULL;
        if(session()->get('researcher', 'NULL') != 'NULL'){
            $researcher = GoldilocksResearcher::where('researcher', session('researcher'))->firstOrFail();
        }
        $listeners = GoldilocksListener::all();
        return view ('goldilocks.researcher-login', compact('listeners', 'researcher'));
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

            //redirect to researcher page with required parameters
            return view('goldilocks.researcher', compact('listener', 'programs', 'researcher', 'parameters'));

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
        return view('goldilocks.listener', compact('listener','parameters', 'program_id'));

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
     * Loads listener goldilocks page for self adjustment.
     *
     * @return View
     */
    public function selfAdjustment(){
        $listener = GoldilocksListener::where('listener', session('listener'))->first();
        $parameters = null;
        $program_id = null;

        //get most recent program for use if it exists
        if($listener->current_program_id != null){
            $program_id = $listener->current_program_id;
            $program = GoldilocksProgram::where('id', $program_id)->first();
            $parameters = $program->parameters;
        }

        return view('goldilocks.listener', compact('listener', 'parameters', 'program_id'));
    }

    /**
     * Loads page with all listener programs.
     *
     * @return View
     */
    public function programsPage(){
        $listener = GoldilocksListener::where('listener', session('listener'))->first();
        $programs = $listener->programs;
        return view('goldilocks.programs', compact('listener', 'programs'));

    }
}