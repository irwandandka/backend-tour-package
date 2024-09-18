<?php

use Illuminate\Support\Facades\Route;

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::middleware('auth:api')->group(function () {
    Route::get('user', 'AuthController@getUser');
});