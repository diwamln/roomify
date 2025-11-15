<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Booking; // <-- Import model Booking

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- TAMBAHKAN RUTE INI ---
// Ini akan menjadi endpoint untuk kalender kita
Route::get('/bookings', function () {
    // Ambil HANYA booking yang sudah 'approved'
    $bookings = Booking::where('status', 'approved')
                        ->with('room') // Ambil data ruangan untuk nama
                        ->get();

    // Ubah format data agar sesuai dengan FullCalendar
    $events = $bookings->map(function ($booking) {
        return [
            'title' => $booking->room->name, // Judul event adalah nama ruangan
            'start' => $booking->tanggal . 'T' . $booking->jam_mulai, // Waktu mulai
            'end'   => $booking->tanggal . 'T' . $booking->jam_selesai, // Waktu selesai
        ];
    });

    // Kembalikan sebagai JSON
    return response()->json($events);
});