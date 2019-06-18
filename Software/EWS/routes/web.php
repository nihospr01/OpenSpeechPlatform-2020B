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

Route::get('abx', function () {
    return view('abx');
});

Route::get('ab', function () {
    return view('ab');
});

Route::get('ab2', function(){
    return view('ab2');
});
Route::get('goldilocks-nav', function(){
	return view('goldilocks-nav');
});

Route::get('passThrough', function(){
	return view('passThrough');
});



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

/** 4AFC web app route */
Route::get('4afc', 'WebController@load4AFC');

/** EMA web app route */
Route::get('ema','WebController@loadEMA');

Route::get('goldilocks-javascript', function(){
	return view('goldilocks-javascript');
});


Route::get('start', function(){
	return view('start');
});

// route::get('ema', function(){
// 	return view('ema');
// });


/** Goldilocks **/
Route::get('/goldilocks', 'Goldilocks\GoldilocksController@index');
Route::get('/goldilocks/admin', 'Goldilocks\GoldilocksController@adminIndex');
Route::resource('/goldilocks/admin/researchers', 'Goldilocks\GoldilocksResearcherController',
                ['only' => ['index', 'store', 'destroy']]);
Route::resource('/goldilocks/admin/listeners', 'Goldilocks\GoldilocksListenerController',
                ['only' => ['index', 'store', 'destroy']]);
Route::resource('/goldilocks/admin/logs', 'Goldilocks\GoldilocksLogController',
                ['only' => ['index']]);
Route::resource('/goldilocks/admin/adjustment_logs', 'Goldilocks\ListenerAdjustmentLogController',
                ['only' => ['index']]);
Route::resource('/goldilocks/admin/programs', 'Goldilocks\GoldilocksProgramController',
                ['only' => ['index', 'destroy']]);
Route::post('/goldilocks/admin/programs/{goldilocksProgram}',
            ['as' => 'programs.delete', 'uses' => 'Goldilocks\GoldilocksProgramController@delete']);
Route::resource('/goldilocks/admin/generic', 'Goldilocks\GoldilocksGenericController',
                ['only' => ['index']]);
Route::put('/goldilocks/admin/generic', 'Goldilocks\GoldilocksGenericController@update');

/** Researcher Goldilocks routes **/
Route::get('/goldilocks/researcher/login', 'Goldilocks\GoldilocksController@researcherLogin');
Route::post('/goldilocks/researcher/login', 'Goldilocks\GoldilocksController@attemptLogin');
Route::get('/goldilocks/researcher', 'Goldilocks\GoldilocksController@researcherPage');
Route::post('/goldilocks/researcher', 'Goldilocks\GoldilocksController@transitionToGoldilocksApp');
Route::post('/goldilocks/listener/programs/current', 'Goldilocks\GoldilocksListenerController@updateCurrentProgram');

/** Listener Goldilocks routes **/
Route::get('goldilocks/listener/login', 'Goldilocks\GoldilocksController@listenerLoginPage');
Route::post('goldilocks/listener/login', 'Goldilocks\GoldilocksController@listenerLogin');
Route::get('goldilocks/listener/logout', 'Goldilocks\GoldilocksController@listenerLogout');

Route::middleware(['auth.goldilocks'])->group(function(){
    Route::get('/goldilocks/listener', 'Goldilocks\GoldilocksController@selfAdjustment');
    Route::post('/goldilocks/listener', 'Goldilocks\GoldilocksProgramController@store');
    Route::put('/goldilocks/listener', 'Goldilocks\GoldilocksProgramController@update');
    Route::middleware(['nocache'])->group(function() {
        Route::get('/goldilocks/listener/programs', 'Goldilocks\GoldilocksController@programsPage');
    });
    Route::post('/goldilocks/listener/programs/transmit', 'Goldilocks\GoldilocksProgramController@transmit');
    Route::post('/goldilocks/listener/programs', 'Goldilocks\GoldilocksController@modifyProgram');
    Route::post('/goldilocks/listener/log', 'Goldilocks\GoldilocksLogController@store');
    Route::post('/goldilocks/listener/adjustmentLog', 'Goldilocks\ListenerAdjustmentLogController@store');
});

/** Goldilocks log/download routes */
Route::get('/goldilocks/logs', 'Goldilocks\GoldilocksController@logIndex');
Route::get('/goldilocks/logs/researchers', 'Goldilocks\GoldilocksResearcherController@downloadResearchers');
Route::get('/goldilocks/logs/listeners', 'Goldilocks\GoldilocksListenerController@downloadListeners');
Route::get('/goldilocks/logs/programs', 'Goldilocks\GoldilocksProgramController@downloadPrograms');
Route::get('/goldilocks/logs/listener-logs', 'Goldilocks\GoldilocksLogController@downloadLogs');
Route::get('/goldilocks/logs/adjustment-logs', 'Goldilocks\ListenerAdjustmentLogController@downloadLogs');






/** Documentation route **/
Route::get('/docs', function(){
    return redirect('/apigen/classes.html');
});
