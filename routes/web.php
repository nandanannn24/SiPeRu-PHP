<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CalendarController;

// ── Public Routes ─────────────────────────────────────────

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// ── Auth Routes (Guest Only) ──────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Password Reset Routes
    Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Authenticated Routes ──────────────────────────────────

Route::middleware('auth')->group(function () {
    // Dashboard (auto-route berdasarkan role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Peminjaman
    Route::get('/peminjaman/create',  [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman',        [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman',         [PeminjamanController::class, 'index'])->name('peminjaman.index');

    // Approval (Kaprodi/Dekan/TU)
    Route::post('/peminjaman/{peminjaman}/approve', [ApprovalController::class, 'approve'])->name('peminjaman.approve');
    Route::post('/peminjaman/{peminjaman}/revise',  [ApprovalController::class, 'revise'])->name('peminjaman.revise');
    Route::post('/peminjaman/{peminjaman}/reject',  [ApprovalController::class, 'reject'])->name('peminjaman.reject');
    
    // Peminjaman Detail & Chat
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('/peminjaman/{peminjaman}/chat', [PeminjamanController::class, 'storeChat'])->name('peminjaman.chat.store');
    Route::get('/peminjaman/{peminjaman}/cetak', [PeminjamanController::class, 'cetakSurat'])->name('peminjaman.cetak');

    // Profile (Tanda Tangan)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/signature', [\App\Http\Controllers\ProfileController::class, 'updateSignature'])->name('profile.signature.update');
});

// ── Public API (Kalender) ─────────────────────────────────

Route::get('/api/kalender', [CalendarController::class, 'index'])->name('api.kalender');
