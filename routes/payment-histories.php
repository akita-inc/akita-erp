<?php
Route::group(['name' => 'payment-histories', 'prefix' => 'payment_histories', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'PaymentHistories@index')->name('payment_histories.list');
    Route::get('/create', 'PaymentHistories@store')->name('payment_histories.create');
    Route::get('/edit/{id}', 'PaymentHistories@store')->name('payment_histories.edit');
    Route::get('/reservation/{id}', 'PaymentHistories@store')->name('payment_histories.reservation');
    Route::get('/reservation-approval/{id}', 'PaymentHistories@store')->name('payment_histories.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','PaymentHistories@getItems')->name("payment_histories.getItems");
        Route::any('/checkIsExist/{id}','PaymentHistories@checkIsExist')->name("payment_histories.checkIsExist");
        Route::any('back-history', ['uses' => 'PaymentHistories@backHistory']);
        Route::any('/submit','PaymentHistories@submit')->name("payment_histories.save");
        Route::get('/delete/{id}', 'PaymentHistories@delete')->name('payment_histories.delete');
        Route::any('/search-sales-lists', 'PaymentHistories@searchSalesLists');
        Route::any('/updateStatus/{id}', 'PaymentHistories@updateStatus');
        Route::any('/mst-customer-list', ['uses' => 'Api\PaymentHistories@getCustomerList']);
        Route::any('/create-csv', 'PaymentHistories@createCSV');

    });
});
