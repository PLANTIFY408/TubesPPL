<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LandController;

Route::get('/', function () {
    return view('Auth.auth');
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/login', function () {
    return view('Auth.auth', ['isRegister' => false]);
})->name('login');

Route::get('/register', function () {
    return view('Auth.auth', ['isRegister' => true]);
})->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/monitoring', [LandController::class, 'index'])->name('monitoring');
    Route::resource('lands', LandController::class);
    Route::get('/lands/{land}/latest-data', [LandController::class, 'getLatestData'])->name('lands.latest-data');
    Route::get('/lands/{land}/sensor-data', [LandController::class, 'getSensorData'])->name('lands.sensor-data');
    
    Route::get('/consultation', function () {
        return view('Consultation.consultation');
    })->name('consultation');
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
