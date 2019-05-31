<?php
Route::group(['name' => 'expense-application', 'prefix' => 'expense_application', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'ExpenseApplicationController@index')->name('expense_application.list');
    Route::get('/create', 'ExpenseApplicationController@store')->name('expense_application.create');
    Route::get('/edit/{id}', 'ExpenseApplicationController@store')->name('expense_application.edit');
    Route::get('/reservation/{id}', 'ExpenseApplicationController@store')->name('expense_application.reservation');
    Route::get('/reservation-approval/{id}', 'ExpenseApplicationController@store')->name('expense_application.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','ExpenseApplicationController@getItems')->name("expense_application.getItems");
        Route::any('/checkIsExist/{id}','ExpenseApplicationController@checkIsExist')->name("expense_application.checkIsExist");
        Route::any('back-history', ['uses' => 'ExpenseApplicationController@backHistory']);
        Route::any('/submit','ExpenseApplicationController@submit')->name("expense_application.save");
        Route::get('/delete/{id}', 'ExpenseApplicationController@delete')->name('expense_application.delete');
        Route::any('/search-sales-lists', 'ExpenseApplicationController@searchSalesLists');
        Route::any('/updateStatus/{id}', 'ExpenseApplicationController@updateStatus');
        Route::any('/mst-customer-list', ['uses' => 'Api\ExpenseApplicationController@getCustomerList']);
        Route::any('/create-csv', 'ExpenseApplicationController@createCSV');

    });
});
