<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User; // <-- TAMBAHKAN INI (walau bisa via relasi)
use App\Notifications\BookingStatusNotification; // <-- TAMBAHKAN INI

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function dashboard()
    {
        $pendingBookings = Booking::where('status', 'pending')
                                  ->with('user', 'room')
                                  ->latest()
                                  ->paginate(10);
        
        return view('admin.dashboard', compact('pendingBookings'));
    }

    /**
     * Menyetujui booking.
     */
    public function approve(Booking $booking)
    {
        // 1. Ubah status
        $booking->update(['status' => 'approved']);

        // 2. Kirim notifikasi ke user yang membuat booking
        $booking->user->notify(new BookingStatusNotification($booking)); // <-- TAMBAHKAN INI

        // 3. Redirect
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil disetujui (Email notifikasi dikirim).');
    }

    /**
     * Menolak booking.
     */
    public function reject(Booking $booking)
    {
        // 1. Ubah status
        $booking->update(['status' => 'rejected']);

        // 2. Kirim notifikasi ke user yang membuat booking
        $booking->user->notify(new BookingStatusNotification($booking)); // <-- TAMBAHKAN INI

        // 3. Redirect
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil ditolak (Email notifikasi dikirim).');
    }
}