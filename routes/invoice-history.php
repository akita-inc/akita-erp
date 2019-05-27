<?php
Route::group(['name' => 'invoice-history', 'prefix' => 'invoice_history', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'InvoiceHistoryController@index')->name('invoice_history.list');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','InvoiceHistoryController@getItems')->name("invoice_history.getItems");
        Route::any('/checkIsExist/{id}','InvoiceHistoryController@checkIsExist')->name("invoice_history.checkIsExist");
        Route::any('back-history', ['uses' => 'InvoiceHistoryController@backHistory']);
        Route::any('load-list-staff', ['uses' => 'InvoiceHistoryController@loadListStaff']);
        Route::any('/submit','InvoiceHistoryController@save')->name("invoice_history.save");
        Route::get('/delete/{id}', 'InvoiceHistoryController@delete')->name('invoice_history.delete');
        Route::get('/getListCustomers', 'InvoiceHistoryController@getListCustomers');
        Route::any('/load-list-bundle-dt', 'InvoiceHistoryController@loadListBundleDt');
        Route::any('/get-details-invoice', 'InvoiceHistoryController@getDetailsInvoice');
        Route::any('/create-pdf', 'InvoiceHistoryController@createPDF');
        Route::any('/create-csv', 'InvoiceHistoryController@createCSV');
        Route::any('/get-first-last-date-previous-month', 'InvoiceHistoryController@getFirstLastDatePreviousMonth');
        Route::any('/create-amazon-csv', 'InvoiceHistoryController@createAmazonCSV');
    });
});