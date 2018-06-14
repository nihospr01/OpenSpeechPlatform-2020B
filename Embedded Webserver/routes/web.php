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

Route::get('/', function () {
    return view('welcome');
});


Route::get('researcherpage', function(){
    return view('researcherpage');
});
Route::post('researcherpage', 'WebController@updateParams');


Route::get('nalnl2', function(){
    return view('nalnl2');
});
Route::post('nalnl2', 'NAL_NL2Controller@getParameters');
Route::post('nalnl2update', 'NAL_NL2Controller@updateParameters');

Route::get('4afc', function(){
	return view('4afc');
});