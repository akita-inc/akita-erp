<?php

Route::group(['name' => 'suppliers', 'prefix' => 'suppliers', 'middleware' => ['web'] ], function () {
    Route::any('/list', 'SuppliersController@index')->name('suppliers.list');
    Route::get('/create', 'SuppliersController@create')->name('suppliers.create');
});