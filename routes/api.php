<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 1;
});

Route::apiResource('user', \App\Http\Controllers\UserController::class);
