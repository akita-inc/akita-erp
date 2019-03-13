<?php

Route::group(['name' => 'vehicles', 'prefix' => 'vehicles', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'VehiclesController@index')->name('vehicles.list');
    Route::get('/create', 'VehiclesController@create')->name('vehicles.create');
    Route::post('/create', 'VehiclesController@create');
    Route::get('/edit/{id}', 'VehiclesController@create')->name('vehicles.edit');
    Route::post('/edit/{id}/{mode}', 'VehiclesController@create')->name('vehicles.edit.post');
    Route::get('/delete/{id}', 'VehiclesController@delete')->name('vehicles.delete');
    Route::post('/delete/{id}', 'VehiclesController@delete')->name('vehicles.delete.post');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','VehiclesController@getItems')->name("vehicles.getItems");
        Route::any('/checkIsExist/{id}','VehiclesController@checkIsExist')->name("vehicles.checkIsExist");
        Route::any('back-history', ['uses' => 'VehiclesController@backHistory']);

    });
});