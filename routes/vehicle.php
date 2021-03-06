<?php

Route::group(['name' => 'vehicles', 'prefix' => 'vehicles', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'VehiclesController@index')->name('vehicles.list');
    Route::get('/create', 'VehiclesController@create')->name('vehicles.create');
    Route::post('/create', 'VehiclesController@create');
    Route::get('/edit/{id}', 'VehiclesController@create')->name('vehicles.edit');
    Route::post('/edit/{id}/{mode}', 'VehiclesController@create')->name('vehicles.edit.post');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','VehiclesController@getItems')->name("vehicles.getItems");
        Route::any('/checkIsExist/{id}','VehiclesController@checkIsExist')->name("vehicles.checkIsExist");
        Route::any('back-history', ['uses' => 'VehiclesController@backHistory']);
        Route::any('load-list-staff', ['uses' => 'VehiclesController@loadListStaff']);
        Route::any('/submit','VehiclesController@save')->name("vehicles.save");
        Route::get('/delete/{id}', 'VehiclesController@delete')->name('vehicles.delete');
    });
});