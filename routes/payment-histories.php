<?php
Route::group(['name' => 'payment-histories', 'prefix' => 'payment_histories', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'PaymentHistoriesController@index')->name('payment_histories.list');
    Route::get('/create', 'PaymentHistoriesController@store')->name('payment_histories.create');
    Route::get('/edit/{id}', 'PaymentHistoriesController@store')->name('payment_histories.edit');
    Route::get('/reservation/{id}', 'PaymentHistoriesController@store')->name('payment_histories.reservation');
    Route::get('/reservation-approval/{id}', 'PaymentHistoriesController@store')->name('payment_histories.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','PaymentHistoriesController@getItems')->name("payment_histories.getItems");
        Route::any('/checkIsExist/{id}','PaymentHistoriesController@checkIsExist')->name("payment_histories.checkIsExist");
        Route::any('back-history', ['uses' => 'PaymentHistoriesController@backHistory']);
        Route::any('/submit','PaymentHistoriesController@submit')->name("payment_histories.save");
        Route::get('/delete/{id}', 'PaymentHistoriesController@delete')->name('payment_histories.delete');
        Route::any('/search-sales-lists', 'PaymentHistoriesController@searchSalesLists');
        Route::any('/details-payment-histories', 'PaymentHistoriesController@getDetailsPaymentHistories');

    });
});
