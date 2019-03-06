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
Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return view('welcome');
    });

});

Route::post('login','Auth\LoginController@postLogin');
Route::get('/login','Auth\LoginController@getLogin')->name('login');
Route::get('/logout','Auth\LoginController@logout')->name('logout');
include "supplier.php";
include "customer.php";
