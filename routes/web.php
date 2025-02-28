<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes untuk Admin
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminController::class, 'login']);
    Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::get('register', [AdminController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('register', [AdminController::class, 'register'])->name('admin.register.submit');
});

// Admin Panel (Hanya untuk Admin)
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manajemen User
    Route::post('add-role', [AdminController::class, 'addRole'])->name('admin.addRole');
    Route::get('edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
});
Route::post('/siswa/withdraw', [WalletController::class, 'withdraw'])->name('siswa.withdraw');
Route::post('/siswa/pay', [WalletController::class, 'pay'])->name('siswa.pay');


// Route Siswa (Hanya untuk Role Siswa)
Route::middleware(['auth'])->prefix('siswa')->group(function () {
    Route::get('/', [AdminController::class, 'siswaPage'])->name('siswa.page');

    //  Route untuk transfer sesama siswa
    Route::post('/transfer', [WalletController::class, 'transferSaldo'])->name('siswa.transfer');
    
});

Route::post('/siswa/topup', [WalletController::class, 'requestTopUp'])->name('siswa.topup');
Route::post('/bank/topup/{id}/confirm', [WalletController::class, 'confirmTopUp'])->name('bank.confirmTopUp');

// Route Bank (Hanya untuk Role Bank)
Route::middleware(['auth'])->prefix('bank')->group(function () {
    Route::get('/', [AdminController::class, 'bankPage'])->name('bank.page');
    Route::post('/add-student', [AdminController::class, 'addStudent'])->name('bank.addStudent');
    Route::delete('/remove-student/{id}', [AdminController::class, 'removeStudent'])->name('bank.removeStudent'); // Perbaikan rute di sini
});


//  Route Wallet
Route::middleware(['auth'])->prefix('wallet')->group(function () {
    Route::post('topup', [WalletController::class, 'topUp'])->name('wallet.topup');
    Route::post('transfer', [WalletController::class, 'transfer'])->name('wallet.transfer');
    Route::post('tariktunai', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::get('riwayat', [WalletController::class, 'transactionHistory'])->name('wallet.history');
});

