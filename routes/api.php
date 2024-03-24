<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return ['message' => "Api's working fine."];
});

Route::post('/user/login', [UserController::class, 'login'])->name('user/login');
Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
Route::post('/user', [UserController::class, 'store'])->name('user.store');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
});
