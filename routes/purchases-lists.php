<?php
Route::group(['name' => 'purchases-lists', 'prefix' => 'purchases_lists', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'PurchasesListsController@index')->name('purchases_lists.list');
    Route::get('/create', 'PurchasesListsController@store')->name('purchases_lists.create');
    Route::get('/edit/{id}', 'PurchasesListsController@store')->name('purchases_lists.edit');
    Route::get('/reservation/{id}', 'PurchasesListsController@store')->name('purchases_lists.reservation');
    Route::get('/reservation-approval/{id}', 'PurchasesListsController@store')->name('purchases_lists.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','PurchasesListsController@getItems')->name("purchases_lists.getItems");
        Route::any('/checkIsExist/{id}','PurchasesListsController@checkIsExist')->name("purchases_lists.checkIsExist");
        Route::any('back-history', ['uses' => 'PurchasesListsController@backHistory']);
        Route::any('/submit','PurchasesListsController@submit')->name("purchases_lists.save");
        Route::get('/delete/{id}', 'PurchasesListsController@delete')->name('purchases_lists.delete');
        Route::any('/search-purchases-lists', 'PurchasesListsController@searchSalesLists');
        Route::any('/updateStatus/{id}', 'PurchasesListsController@updateStatus');
        Route::any('/mst-supplier-list', ['uses' => 'Api\PurchasesListsController@getSupplierList']);
        Route::any('/create-csv', 'PurchasesListsController@createCSV');

    });
});
