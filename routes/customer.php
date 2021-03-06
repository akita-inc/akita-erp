<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:20 PM
 */
Route::group(['name' => 'customers', 'prefix'=>'/customers', 'middleware' => ['auth']],function (){
    Route::get('/list','CustomersController@index')->name("customers.list");
    Route::get('/create','CustomersController@store')->name("customers.create");
    Route::get('/edit/{id}', 'CustomersController@store')->name('customers.edit');
    Route::get('/delete/{id}', 'CustomersController@delete')->name('customers.delete');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','CustomersController@getItems')->name("customers.getItems");
        Route::any('/checkIsExist/{id}','CustomersController@checkIsExist')->name("customers.checkIsExist");
        Route::any('/submit','CustomersController@submit')->name("customers.validForm");
        Route::any('/getListBill/{id}','CustomersController@getListBill')->name("customers.getListBill");
        Route::any('back-history', ['uses' => 'CustomersController@backHistory']);
    });
});

