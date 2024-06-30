<?php

use App\Events\UserRegistrationCompletedEvent;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

//use Illuminate\Support\Facades\Artisan;

//Route::get('/cleareverything', function () {
//    $clearcache = Artisan::call('cache:clear');
//    echo "Cache cleared<br>";
//
//    $clearview = Artisan::call('view:clear');
//    echo "View cleared<br>";
//
//    $clearconfig = Artisan::call('config:clear');
//    $clearconfig = Artisan::call('passport:install');
//    echo "Config cleared<br>";
//});
//
Route::get('/add-admin', function () {
    $admins = [
        [
            'name' => 'Admin',
            'address' => 'Khulshi Mart Admin',
            'phone' => '01521487616',
            'email' => 'it@khulshimart.com',
            'birthDate' => '2010-01-01',
            'password' => \Illuminate\Support\Facades\Hash::make('mplit@2024')
        ],
        [
            'name' => 'Admin',
            'address' => 'Khulshi Mart Admin',
            'phone' => '01521487616',
            'email' => 'admin@khulshimart.com',
            'birthDate' => '2010-01-01',
            'password' => \Illuminate\Support\Facades\Hash::make('khulshi@321')
        ],
    ];
    foreach ($admins as $admin) {
        $existingUser = \App\Models\User::where('email', $admin['email'])->first();
        if ($existingUser == null) {
            \App\Models\User::create($admin);
        }
    }
    echo "Admin added<br>";
});
Route::get('/email', function () {
    event(new UserRegistrationCompletedEvent(\App\Models\User::find(1)));
    echo "email sent<br>";
});

//Route::get('/install-passport', function () {
//    // Execute the passport:install Artisan command
//    Artisan::call('passport:install');
//
//    // Capture the output of the command
//    $output = Artisan::output();
//
//    // Output a message indicating the installation
//    echo "Passport installed: <br>";
//    echo $output;
//});
Route::get('/', function (Request $request) {
    return ['message' => "Api is working fine."];
});

Route::get('/migrate', function (Request $request) {
    Artisan::call('migrate');

    return 'Migration completed successfully.';
});

Route::post('/user/login', [UserController::class, 'login'])->name('user/login');
Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
Route::post('/user', [UserController::class, 'store'])->name('user.store');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/otp-list', [UserController::class, 'indexOtp'])->name('user.otp.index');
});
