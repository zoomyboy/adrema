<?php 

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/', 'HomeController')->name('home');
});

