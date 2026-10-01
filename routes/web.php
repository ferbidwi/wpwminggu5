<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN IMPORT INI
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KasirController;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;

// --- RUTE AUTENTIKASI ---
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE DILINDUNGI ---
Route::middleware('auth')->group(function () {

    // DASHBOARD dengan statistik real
    Route::get('/admin', function () {
        $todaySales = Transaction::whereDate('created_at', today())->sum('grand_total');
        $todayCount = Transaction::whereDate('created_at', today())->count();
        $totalProducts = Product::count();
        $lowStock = Product::where('stock', '<', 10)->count();
        $bestSeller = TransactionDetail::select('product_name', DB::raw('SUM(qty) as total'))
            ->groupBy('product_name')
            ->orderBy('total', 'desc')
            ->first();
        return view('admin', compact('todaySales', 'todayCount', 'totalProducts', 'lowStock', 'bestSeller'));
    })->name('admin');

    // CRUD BUKU
    Route::resource('buku', BukuController::class);

    // KASIR
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::get('/api/cari-produk', [KasirController::class, 'search'])->name('kasir.search');
    Route::post('/kasir/transaksi', [KasirController::class, 'store'])->name('kasir.store');
    Route::get('/kasir/struk/{id}', [KasirController::class, 'cetakStruk'])->name('kasir.struk');
    Route::get('/kasir/riwayat', [KasirController::class, 'riwayat'])->name('kasir.riwayat');
    Route::get('/kasir/detail/{id}', [KasirController::class, 'detail'])->name('kasir.detail');

    // Menu Lainnya
    Route::view('/buttons', 'buttons');
    Route::view('/cards', 'cards');
    Route::view('/utilities-color', 'utilities-color');
    Route::view('/utilities-border', 'utilities-border');
    Route::view('/utilities-animation', 'utilities-animation');
    Route::view('/utilities-other', 'utilities-other');
    Route::view('/forgot-password', 'forgot-password');
    Route::view('/404', '404');
    Route::view('/blank', 'blank');
    Route::view('/charts', 'charts');
    Route::view('/tables', 'tables');
});

Route::get('/', function () { return view('frontend'); });