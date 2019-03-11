<?php

Route::group(['name' => 'suppliers', 'prefix' => 'suppliers', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'SuppliersController@index')->name('suppliers.list');
    Route::get('/create', 'SuppliersController@create')->name('suppliers.create');
    Route::post('/create', 'SuppliersController@create');
    Route::get('/edit/{id}', 'SuppliersController@create')->name('suppliers.edit');
    Route::post('/edit/{id}/{mode}', 'SuppliersController@create')->name('suppliers.edit.post');
    Route::get('/delete/{id}', 'SuppliersController@delete')->name('suppliers.delete');
    Route::post('/delete/{id}', 'SuppliersController@delete')->name('suppliers.delete.post');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','SuppliersController@getItems')->name("suppliers.getItems");

    });
});