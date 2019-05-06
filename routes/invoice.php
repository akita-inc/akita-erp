<?php
Route::group(['name' => 'invoices', 'prefix' => 'invoices', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'InvoicesController@index')->name('invoices.list');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','InvoicesController@getItems')->name("invoices.getItems");
        Route::any('/checkIsExist/{id}','InvoicesController@checkIsExist')->name("invoices.checkIsExist");
        Route::any('back-history', ['uses' => 'InvoicesController@backHistory']);
        Route::any('load-list-staff', ['uses' => 'InvoicesController@loadListStaff']);
        Route::any('/submit','InvoicesController@save')->name("invoices.save");
        Route::get('/delete/{id}', 'InvoicesController@delete')->name('invoices.delete');
        Route::get('/getListCustomers', 'InvoicesController@getListCustomers');
        Route::any('/load-list-bundle-dt', 'InvoicesController@loadListBundleDt');
        Route::any('/get-details-invoice', 'InvoicesController@getDetailsInvoice');
        Route::any('/create-pdf', 'InvoicesController@createPDF');
        Route::any('/create-csv', 'InvoicesController@createCSV');
    });
});