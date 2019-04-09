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

Route::group(array('prefix' => 'api/supplier'), function() {
    Route::any('convert-to-kana', ['uses' => 'Api\SuppliersController@convertToKana']);
    Route::any('back-history', ['uses' => 'Api\SuppliersController@backHistory']);
    Route::any('checkIsExist/{id}', ['uses' => 'SuppliersController@checkIsExist']);
});
