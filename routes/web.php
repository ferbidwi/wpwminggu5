<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController; // <-- TAMBAHAN IMPORT

// --- RUTE AUTENTIKASI ---
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- RUTE YANG DILINDUNGI (Hanya bisa diakses setelah login) ---
Route::middleware('auth')->group(function () {
    Route::view('/admin', 'admin')->name('admin');
    
    // RUTE CRUD BUKU (TAMBAHAN)
    Route::resource('buku', BukuController::class);

    // Menu Components
    Route::view('/buttons', 'buttons');
    Route::view('/cards', 'cards');

    // Menu Utilities
    Route::view('/utilities-color', 'utilities-color');
    Route::view('/utilities-border', 'utilities-border');
    Route::view('/utilities-animation', 'utilities-animation');
    Route::view('/utilities-other', 'utilities-other');

    // Menu Addons
    Route::view('/forgot-password', 'forgot-password');
    Route::view('/404', '404');
    Route::view('/blank', 'blank');

    // Charts & Tables
    Route::view('/charts', 'charts');
    Route::view('/tables', 'tables');
});

// --- RUTE PUBLIK ---
Route::get('/', function () { return view('frontend'); });