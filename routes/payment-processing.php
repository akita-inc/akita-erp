<?php
Route::group(['name' => 'payment_processing', 'prefix' => 'payment_processing', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'PaymentProcessingController@index')->name('payment_processing.list');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','PaymentProcessingController@getItems')->name("payment_processing.getItems");
        Route::any('/checkIsExist/{id}','PaymentProcessingController@checkIsExist')->name("payment_processing.checkIsExist");
        Route::any('back-history', ['uses' => 'PaymentProcessingController@backHistory']);
        Route::any('load-list-staff', ['uses' => 'PaymentProcessingController@loadListStaff']);
        Route::any('/submit','PaymentProcessingController@save')->name("payment_processing.save");
        Route::get('/delete/{id}', 'PaymentProcessingController@delete')->name('payment_processing.delete');
        Route::get('/getListCustomers', 'PaymentProcessingController@getListCustomers');
        Route::any('/load-list-bundle-dt', 'PaymentProcessingController@loadListBundleDt');
        Route::any('/get-details-invoice', 'PaymentProcessingController@getDetailsInvoice');
        Route::any('/create-pdf', 'PaymentProcessingController@createPDF');
        Route::any('/create-csv', 'PaymentProcessingController@createCSV');
        Route::any('/get-current-year-month', 'PaymentProcessingController@getCurrentYearMonth');
        Route::any('/create-amazon-csv', 'PaymentProcessingController@createAmazonCSV');
    });
});