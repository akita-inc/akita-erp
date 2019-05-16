<?php
Route::group(['name' => 'work-flow', 'prefix' => 'work_flow', 'middleware' => ['auth'] ], function () {
    Route::any('/list', 'WorkFlowController@index')->name('work_flow.list');
    Route::get('/create', 'WorkFlowController@store')->name('work_flow.create');
    Route::get('/edit/{id}', 'WorkFlowController@store')->name('work_flow.edit');
    Route::get('/reservation/{id}', 'WorkFlowController@store')->name('work_flow.reservation');
    Route::get('/reservation-approval/{id}', 'WorkFlowController@store')->name('work_flow.reservation_approval');

    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::post('/getItems','WorkFlowController@getItems')->name("work_flow.getItems");
        Route::any('/checkIsExist/{id}','WorkFlowController@checkIsExist')->name("work_flow.checkIsExist");
        Route::any('back-history', ['uses' => 'WorkFlowController@backHistory']);
        Route::any('/submit','WorkFlowController@submit')->name("work_flow.save");
        Route::get('/delete/{id}', 'WorkFlowController@delete')->name('work_flow.delete');
        Route::any('/get-list-wf-applicant-affiliation-classification', 'WorkFlowController@getListWfApplicantAffiliationClassification');
        Route::any('/validate-data', 'WorkFlowController@validateData');
    });
});
