<?php
Route::group(['name' => 'take-vacation', 'prefix' => 'take_vacation', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'TakeVacationController@index')->name('take_vacation.list');
    Route::get('/create', 'TakeVacationController@store')->name('take_vacation.create');
    Route::get('/edit/{id}', 'TakeVacationController@store')->name('take_vacation.edit');
    Route::get('/reservation/{id}', 'TakeVacationController@store')->name('take_vacation.reservation');
    Route::get('/reservation-approval/{id}', 'TakeVacationController@store')->name('take_vacation.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','TakeVacationController@getItems')->name("take_vacation.getItems");
        Route::any('/checkIsExist/{id}','TakeVacationController@checkIsExist')->name("take_vacation.checkIsExist");
        Route::any('back-history', ['uses' => 'TakeVacationController@backHistory']);
        Route::any('/submit','TakeVacationController@submit')->name("take_vacation.save");
        Route::get('/delete/{id}', 'TakeVacationController@delete')->name('take_vacation.delete');
        Route::any('/search-sales-lists', 'TakeVacationController@searchSalesLists');
        Route::any('/search-staff', 'TakeVacationController@searchStaff');
    });
});
