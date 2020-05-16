<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/users', 'UserController@store')->name('register');
Route::post('/users/verify', 'UserController@verify')->name('login');

Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/users', 'UserController@show');
    Route::post('/users/transfer', 'UserController@transfer');
    Route::get('/users/categories', 'UserController@showCategories');

    Route::get('/events', 'EventController@index');
    Route::get('/events/{id}', 'EventController@show');
    Route::post('/events/buy', 'EventController@buy');
});
