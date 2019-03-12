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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(array('prefix' => 'api/supplier'), function() {
    Route::any('convert-to-kana', ['uses' => 'Api\SuppliersController@convertToKana']);
    Route::any('checkIsExist/{id}', ['uses' => 'Api\SuppliersController@checkIsExist']);
});