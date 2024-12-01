<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Log::channel('testing')->info('Route Accessed');
    return view('welcome');
});
