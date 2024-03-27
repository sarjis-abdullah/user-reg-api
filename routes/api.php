<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:clear');
    $clearconfig = Artisan::call('passport:install');
    echo "Config cleared<br>";
});

Route::get('/add-admin', function () {
    \App\Models\User::create([
        'name' => 'Admin',
        'address' => 'Khulshi Mart Admin',
        'phone' => '01521487616',
        'email' => 'it@khulshimart.com',
        'birthDate' => '2010-01-01',
        'password' => \Illuminate\Support\Facades\Hash::make('mplit@2024')
    ]);
    \App\Models\User::create([
        'name' => 'Admin',
        'address' => 'Khulshi Mart Admin',
        'phone' => '01521487616',
        'email' => 'admin@khulshimart.com',
        'birthDate' => '2010-01-01',
        'password' => \Illuminate\Support\Facades\Hash::make('khulshi@321')
    ]);
    echo "Admin added<br>";
});

Route::get('/install-passport', function () {
    // Execute the passport:install Artisan command
    Artisan::call('passport:install');

    // Capture the output of the command
    $output = Artisan::output();

    // Output a message indicating the installation
    echo "Passport installed: <br>";
    echo $output;
});
Route::get('/test', function (Request $request) {
    return ['message' => "Api's working fine."];
});

Route::post('/user/login', [UserController::class, 'login'])->name('user/login');
Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
Route::post('/user', [UserController::class, 'store'])->name('user.store');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
});
