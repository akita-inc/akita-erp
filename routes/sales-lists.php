<?php
Route::group(['name' => 'sales-lists', 'prefix' => 'sales_lists', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'SalesListsController@index')->name('sales_lists.list');
    Route::get('/create', 'SalesListsController@store')->name('sales_lists.create');
    Route::get('/edit/{id}', 'SalesListsController@store')->name('sales_lists.edit');
    Route::get('/reservation/{id}', 'SalesListsController@store')->name('sales_lists.reservation');
    Route::get('/reservation-approval/{id}', 'SalesListsController@store')->name('sales_lists.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','SalesListsController@getItems')->name("sales_lists.getItems");
        Route::any('/checkIsExist/{id}','SalesListsController@checkIsExist')->name("sales_lists.checkIsExist");
        Route::any('back-history', ['uses' => 'SalesListsController@backHistory']);
        Route::any('/submit','SalesListsController@submit')->name("sales_lists.save");
        Route::get('/delete/{id}', 'SalesListsController@delete')->name('sales_lists.delete');
        Route::any('/search-sales-lists', 'SalesListsController@searchSalesLists');
        Route::any('/updateStatus/{id}', 'SalesListsController@updateStatus');
        Route::any('/mst-customer-list', ['uses' => 'Api\SalesListsController@getCustomerList']);
        Route::any('/export-csv', ['uses' => 'Api\SalesListsController@exportCSV']);

    });
});
