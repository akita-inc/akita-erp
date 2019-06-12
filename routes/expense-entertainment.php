<?php
Route::group(['name' => 'expense-entertainment', 'prefix' => 'expense_entertainment', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'ExpenseEntertainmentController@index')->name('expense_entertainment.list');
    Route::get('/create', 'ExpenseEntertainmentController@store')->name('expense_entertainment.create');
    Route::get('/edit/{id}', 'ExpenseEntertainmentController@store')->name('expense_entertainment.edit');
    Route::get('/approval/{id}', 'ExpenseEntertainmentController@store')->name('expense_entertainment.approval');
    Route::get('/reference/{id}', 'ExpenseEntertainmentController@store')->name('expense_entertainment.reference');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','ExpenseEntertainmentController@getItems')->name("expense_entertainment.getItems");
        Route::any('/checkIsExist/{id}','ExpenseEntertainmentController@checkIsExist')->name("expense_entertainment.checkIsExist");
        Route::any('back-history', ['uses' => 'ExpenseEntertainmentController@backHistory']);
        Route::any('/submit','ExpenseEntertainmentController@submit')->name("expense_entertainment.save");
        Route::get('/delete/{id}', 'ExpenseEntertainmentController@delete')->name('expense_entertainment.delete');
        Route::any('/search-sales-lists', 'ExpenseEntertainmentController@searchSalesLists');
        Route::any('/search-staff', 'ExpenseEntertainmentController@searchStaff');
        Route::any('/search-entertainment', 'ExpenseEntertainmentController@searchEntertainment');
    });
});
