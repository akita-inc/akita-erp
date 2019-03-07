<?php

Route::group(['name' => 'staffs', 'prefix'=>'/staffs', 'middleware' => ['web']],function (){
    Route::get('/','StaffsController@index')->name("staffs.list");
    Route::get('/create','StaffsController@create')->name("staffs.create");

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','StaffsController@getItems')->name("staffs.getItems");

    });
});

