<?php

Route::group(['name' => 'staffs', 'prefix'=>'/staffs', 'middleware' => ['auth']],function (){
    Route::get('/list','StaffsController@index')->name("staffs.list");
    Route::get('/create','StaffsController@store')->name("staffs.create");
    Route::get('/edit/{id}', 'StaffsController@store')->name('staffs.edit');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','StaffsController@getItems')->name("staffs.getItems");
        Route::get('/delete/{id}','StaffsController@delete')->name("staffs.delete");
        Route::any('/submit','StaffsController@submit')->name("staffs.validForm");
        Route::any('/list-staff-job-ex/{id}','StaffsController@getStaffJobEx')->name("staffs.getListBill");
        Route::any('/list-staff-qualification/{id}','StaffsController@getStaffQualifications')->name("staffs.getStaffQualifications");
        Route::any('/list-staff-dependents/{id}','StaffsController@getStaffDependents')->name("staffs.getStaffDependents");
        Route::any('/list-staff-auths/{id}','StaffsController@getStaffAuths')->name("staffs.getStaffAuths");
        Route::any('/checkIsExist/{id}','StaffsController@checkIsExist')->name("staffs.checkIsExist");
        Route::any('back-history', ['uses' => 'StaffsController@backHistory']);
        Route::any('/relocation-municipal-office', ['uses' => 'Api\StaffsController@getListReMunicipalOffice']);
        Route::any('/load-role-config', ['uses' => 'Api\StaffsController@getRoleConfig']);
    });
});

