<?php
Route::group(['name' => 'work-flow', 'prefix' => 'work-flow', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'WorkFlowController@index')->name('work-flow.list');
    Route::get('/create', 'WorkFlowController@store')->name('work-flow.create');
    Route::get('/edit/{id}', 'WorkFlowController@store')->name('work-flow.edit');
    Route::get('/reservation/{id}', 'WorkFlowController@store')->name('work-flow.reservation');
    Route::get('/reservation-approval/{id}', 'WorkFlowController@store')->name('work-flow.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','WorkFlowController@getItems')->name("work-flow.getItems");
        Route::any('/checkIsExist/{id}','WorkFlowController@checkIsExist')->name("work-flow.checkIsExist");
        Route::any('back-history', ['uses' => 'WorkFlowController@backHistory']);
        Route::any('/submit','WorkFlowController@submit')->name("work-flow.save");
        Route::get('/delete/{id}', 'WorkFlowController@delete')->name('work-flow.delete');
        Route::any('/search-sales-lists', 'WorkFlowController@searchSalesLists');
    });
});
