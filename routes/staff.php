<?php

Route::group(['name' => 'staffs', 'prefix'=>'/staffs', 'middleware' => ['auth']],function (){
    Route::get('/','StaffsController@index')->name("staffs.list");
    Route::get('/create','StaffsController@create')->name("staffs.create");

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','StaffsController@getItems')->name("staffs.getItems");
        Route::get('/delete/{id}','StaffsController@delete')->name("staffs.delete");
        Route::any('/submit','StaffsController@submit')->name("staffs.validForm");
        Route::any('/checkIsExist/{id}','StaffsController@checkIsExist')->name("staffs.checkIsExist");
        Route::any('back-history', ['uses' => 'StaffsController@backHistory']);
        Route::any('/relocation-municipal-office', ['uses' => 'Api\StaffsController@getListReMunicipalOffice']);
        Route::any('/load-role-config', ['uses' => 'Api\StaffsController@getRoleConfig']);
    });
});

