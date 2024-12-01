<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/laravel-cibaii', function () {
    return 'Laravel CIBAI';
});
