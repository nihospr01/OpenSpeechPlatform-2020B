<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// This is a default route included in Laravel. We have not removed it to 
// serve as an example of what a request can look like
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('connect', 'ApiController@connect');

// Route::post('disconnect', 'ApiController@disconnect');

Route::post('params', 'ApiController@updateParameters');
Route::post('paramsUncalibrated', 'ApiController@updateParametersUncalibrated');

Route::get('getParams', 'ApiController@getParameters');

Route::post('calibration', 'ApiController@setCalibrationParameters');

