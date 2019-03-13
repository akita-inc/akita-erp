<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:20 PM
 */
Route::group(['name' => 'customers', 'prefix'=>'/home', 'middleware' => ['auth']],function (){
    /*Api using Vue*/
    Route::group(['prefix' => 'api-v1'], function () {
        Route::any('/convertKana','HomeController@convertKana')->name("home.convertKana");
    });
});

