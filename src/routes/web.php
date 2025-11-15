<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CalendarController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// == RUTE PUBLIK ==
// (Bisa diakses oleh semua orang, termasuk tamu)
Route::get('/', function () {
    return view('welcome');
});

// == RUTE UNTUK SEMUA USER YANG SUDAH LOGIN ==
// (Dilindungi oleh 'auth' dan 'verified')
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard biasa
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Halaman Profile (Edit, Update, Delete)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman Booking (Semua user bisa booking & lihat booking mereka)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    // == RUTE KHUSUS ADMIN ==
    // (Dilindungi tambahan oleh middleware 'admin')
    Route::middleware(['admin'])->group(function () {

        // Halaman Dashboard Admin
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Halaman Kelola Ruangan (CRUD)
        // INI SATU-SATUNYA TEMPAT Route::resource('rooms', ...) HARUS ADA
        Route::resource('rooms', RoomController::class);

        // Rute untuk Aksi Admin (Approve / Reject)
        Route::patch('/admin/bookings/{booking}/approve', [AdminController::class, 'approve'])->name('admin.bookings.approve');
        Route::patch('/admin/bookings/{booking}/reject', [AdminController::class, 'reject'])->name('admin.bookings.reject');
    });

});


// Rute Autentikasi (Login, Register, dll.)
require __DIR__.'/auth.php';