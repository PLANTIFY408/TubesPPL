<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('Auth.auth');
});

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/products', function () {
    return view('Product.products');
})->name('products');


Route::get('/login', function () {
    return view('Auth.auth', ['isRegister' => false]);
})->name('login');

Route::get('/register', function () {
    return view('Auth.auth', ['isRegister' => true]);
})->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/monitoring', function () {
        return view('Monitoring.monitorings');
    })->name('monitoring');
    
    Route::get('/consultation', function () {
        return view('Consultation.consultation');
    })->name('consultation');
    
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
