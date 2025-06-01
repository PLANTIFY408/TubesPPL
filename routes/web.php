<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\ConsultationController;

Route::get('/', function () {
    return view('home');
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
    
    Route::get('/consultation', [ConsultationController::class, 'index'])->name('consultation.index');
    Route::get('/consultation/chat/{expertId}', [ConsultationController::class, 'showChat'])->name('consultation.chat');
    Route::post('/consultation/chat/{expertId}', [ConsultationController::class, 'sendMessage'])->name('consultation.sendMessage');
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/lands/create', [LandController::class, 'create'])->name('lands.create');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');

    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/upload-payment', [OrderController::class, 'uploadPayment'])->name('orders.upload-payment');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
});

// Admin routes
Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('orders', AdminOrderController::class);
});
