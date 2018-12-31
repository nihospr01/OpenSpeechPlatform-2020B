<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WebController@welcomePage');
Route::get('ansi', 'WebController@ansiPage');


/** Researcher routes **/
Route::get('researcher/amplification', 'WebController@researcherAmplificationPage');
Route::get('researcher/feedback', 'WebController@researcherFeedbackPage');
Route::get('researcher/noise', 'WebController@researcherNoisePage');

Route::get('researcherpage', 'WebController@researcherPage');
Route::post('researcherpage', 'WebController@updateParams');



/** nal-nl2 routes **/
route::get('nal-nl2', 'NALNL2\NALNL2Controller@loadPage');
Route::post('nal-nl2', 'NALNL2\NALNL2Controller@getParameters');
Route::post('nal-nl2update', 'NALNL2\NALNL2Controller@updateParameters');





route::get('4afc', 'WebController@load4AFC');


Route::get('goldilocks-javascript', function(){
	return view('goldilocks-javascript');
});


Route::get('start', function(){
	return view('start');
});


/** Goldilocks **/
Route::get('/goldilocks', 'Goldilocks\GoldilocksController@index');
Route::get('/goldilocks/admin', 'Goldilocks\GoldilocksController@adminIndex');
Route::resource('/goldilocks/admin/researchers', 'Goldilocks\GoldilocksResearcherController', ['only' => ['index', 'store']]);
Route::resource('/goldilocks/admin/listeners', 'Goldilocks\GoldilocksListenerController', ['only' => ['index', 'store']]);
Route::resource('/goldilocks/admin/logs', 'Goldilocks\GoldilocksLogController', ['only' => ['index']]);
Route::resource('/goldilocks/admin/programs', 'Goldilocks\GoldilocksProgramController', ['only' => 'index']);

/** Researcher Goldilocks routes **/
Route::get('/goldilocks/researcher/login', 'Goldilocks\GoldilocksController@researcherLogin');
Route::post('/goldilocks/researcher/login', 'Goldilocks\GoldilocksController@attemptLogin');
Route::get('/goldilocks/researcher', 'Goldilocks\GoldilocksController@researcherPage');
Route::post('/goldilocks/researcher', 'Goldilocks\GoldilocksController@transitionToGoldilocksApp');
Route::post('/goldilocks/listener/programs/current', 'Goldilocks\GoldilocksListenerController@updateCurrentProgram');

/** Listener Goldilocks routes **/
Route::get('goldilocks/listener/login', 'Goldilocks\GoldilocksController@listenerLoginPage');
Route::post('goldilocks/listener/login', 'Goldilocks\GoldilocksController@listenerLogin');

Route::middleware(['auth.goldilocks'])->group(function(){
    Route::get('/goldilocks/listener', 'Goldilocks\GoldilocksController@selfAdjustment');
    Route::post('/goldilocks/listener', 'Goldilocks\GoldilocksProgramController@store');
    Route::put('/goldilocks/listener', 'Goldilocks\GoldilocksProgramController@update');
    Route::get('/goldilocks/listener/programs', 'Goldilocks\GoldilocksController@programsPage');
    Route::post('/goldilocks/listener/programs/transmit', 'Goldilocks\GoldilocksProgramController@transmit');
    //Route::post('programs', 'GoldilocksController@modifyProgram');
    Route::post('/goldilocks/listener/log', 'Goldilocks\GoldilocksLogController@store');
});

/** Goldilocks log/download routes */
Route::get('/goldilocks/logs', 'Goldilocks\GoldilocksController@logIndex');
Route::get('/goldilocks/logs/researchers', 'Goldilocks\GoldilocksResearcherController@downloadResearchers');
Route::get('/goldilocks/logs/listeners', 'Goldilocks\GoldilocksListenerController@downloadListeners');
Route::get('/goldilocks/logs/programs', 'Goldilocks\GoldilocksProgramController@downloadPrograms');
Route::get('/goldilocks/logs/listener-logs', 'Goldilocks\GoldilocksLogController@downloadLogs');






/** Documentation route **/
Route::get('/docs', function(){
    return redirect('/apigen/classes.html');
});
