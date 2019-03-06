<?php

Route::group(['name' => 'suppliers', 'prefix' => 'suppliers', 'middleware' => ['web'] ], function () {
    Route::get('/list', 'SuppliersController@index')->name('suppliers.list');
    Route::get('/create', 'SuppliersController@create')->name('suppliers.create');
});