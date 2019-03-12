<?php

Route::group(['name' => 'vehicles', 'prefix' => 'vehicles', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'VehiclesController@index')->name('vehicles.list');
    Route::get('/create', 'VehiclesController@create')->name('vehicles.create');
    Route::post('/create', 'VehiclesController@create');
    Route::get('/delete/{id}', 'VehiclesController@delete')->name('vehicles.delete');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','VehiclesController@getItems')->name("vehicles.getItems");
        Route::any('/checkIsExist/{id}','VehiclesController@checkIsExist')->name("vehicles.checkIsExist");
    });
});