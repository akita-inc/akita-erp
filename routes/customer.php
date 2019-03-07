<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:20 PM
 */
Route::group(['name' => 'customers', 'prefix'=>'/customers', 'middleware' => ['web']],function (){
    Route::get('/','CustomersController@index')->name("customers.list");
    Route::get('/create','CustomersController@create')->name("customers.create");
    Route::get('/delete/{id}', 'CustomersController@delete')->name('customers.delete');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','CustomersController@getItems')->name("customers.getItems");

    });
});

