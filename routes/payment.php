<?php

Route::group(['name' => 'payments', 'prefix'=>'/payments', 'middleware' => ['auth']],function (){
    Route::get('/list','PaymentsController@index')->name("payments.list");

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','PaymentsController@getItems')->name("payments.getItems");
        Route::any('/get-details-payment', 'PaymentsController@getDetailsPayment');
        Route::any('/load-list-bundle-dt', 'PaymentsController@loadListBundleDt');
        Route::any('/get-list-suppliers', 'PaymentsController@getListSuppliers');
        Route::any('/execution', 'PaymentsController@execution');
    });
});