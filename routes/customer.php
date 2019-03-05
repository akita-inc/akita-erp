<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:20 PM
 */
Route::group(['prefix'=>'/customers'],function (){
    Route::get('/','CustomersController@create')->name("customers.create");
    /*Route::group(['prefix' => 'api-v1'], function () {
    });*/
});

