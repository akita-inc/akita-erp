<?php
Route::group(['name' => 'empty-info', 'prefix' => 'empty_info', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'EmptyInfoController@index')->name('empty_info.list');
    Route::get('/create', 'EmptyInfoController@store')->name('empty_info.create');
    Route::get('/edit/{id}', 'EmptyInfoController@store')->name('empty_info.edit');
    Route::get('/reservation/{id}', 'EmptyInfoController@store')->name('empty_info.reservation');
    Route::get('/reservation-approval/{id}', 'EmptyInfoController@store')->name('empty_info.reservation_approval');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','EmptyInfoController@getItems')->name("empty_info.getItems");
        Route::any('/checkIsExist/{id}','EmptyInfoController@checkIsExist')->name("empty_info.checkIsExist");
        Route::any('back-history', ['uses' => 'EmptyInfoController@backHistory']);
        Route::any('/submit','EmptyInfoController@submit')->name("empty_info.save");
        Route::get('/delete/{id}', 'EmptyInfoController@delete')->name('empty_info.delete');
        Route::any('/search-vehicle', 'EmptyInfoController@searchVehicle');
        Route::any('/updateStatus/{id}', 'EmptyInfoController@updateStatus');
    });
});
