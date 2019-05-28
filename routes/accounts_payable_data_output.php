<?php
Route::group(['name' => 'accounts-payable-data-output', 'prefix' => 'accounts_payable_data_output', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'AccountsPayableDataOutputController@index')->name('accounts_payable_data_output.list');


    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::any('/load-list-bundle-dt', 'AccountsPayableDataOutputController@loadListBundleDt');
        Route::any('/create-csv', 'AccountsPayableDataOutputController@createCSV');
        Route::any('/get-current-year-month', 'AccountsPayableDataOutputController@getCurrentYearMonth');
    });
});