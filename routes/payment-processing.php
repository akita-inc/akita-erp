<?php
Route::group(['name' => 'payment_processing', 'prefix' => 'payment_processing', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'PaymentProcessingController@index')->name('payment_processing.list');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','PaymentProcessingController@getItems')->name("payment_processing.getItems");
        Route::any('/checkIsExist/{id}','PaymentProcessingController@checkIsExist')->name("payment_processing.checkIsExist");
        Route::any('back-history', ['uses' => 'PaymentProcessingController@backHistory']);
        Route::any('load-list-staff', ['uses' => 'PaymentProcessingController@loadListStaff']);
        Route::any('/submit','PaymentProcessingController@submit')->name("payment_processing.submit");
        Route::get('/getListCustomers', 'PaymentProcessingController@getListCustomers');
        Route::any('/get-current-year-month', 'PaymentProcessingController@getCurrentYearMonth');
    });
});