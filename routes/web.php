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
Route::group(['middleware' => 'UpdateLogRouters'], function () {
    Route::group(['middleware' => ['StaffUpdateMiddleware','auth']], function () {
        Route::get('/', 'WelcomeController@index');

    });

    include "api.php";
    Route::post('login','Auth\LoginController@postLogin');
    Route::get('/login','Auth\LoginController@getLogin')->name('login');
    Route::get('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/logoutError','Auth\LoginController@logoutError')->name('logoutError');
    Route::group(['middleware' => 'StaffUpdateMiddleware'], function () {
        include "home.php";
        include "supplier.php";
        include "customer.php";
        include "staff.php";
        include "vehicle.php";
        include "empty-info.php";
        include "invoice.php";
        include "sales-lists.php";
        include "purchases-lists.php";
        include "payment.php";
        include "work-flow.php";
        include "take-vacation.php";
        include "invoice-history.php";
        include "payment-histories.php";
        include "accounts_payable_data_output.php";
        include "payment-processing.php";
    });
});
