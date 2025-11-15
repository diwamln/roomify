<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking; // <-- Import model Booking

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan semua booking 'pending'.
     */
    public function dashboard()
    {
        // Ambil semua booking yang statusnya 'pending'
        // 'with' digunakan agar data user dan room ikut terambil (menghindari N+1 query)
        // 'latest()' agar yang terbaru di atas
        $pendingBookings = Booking::where('status', 'pending')
                                  ->with('user', 'room')
                                  ->latest()
                                  ->paginate(10); // Kita pakai paginate agar rapi
        
        // Kirim data ke view
        return view('admin.dashboard', compact('pendingBookings'));
    }

    /**
     * Menyetujui booking.
     */
    public function approve(Booking $booking)
    {
        // Ganti statusnya menjadi 'approved'
        $booking->update(['status' => 'approved']);
        
        // Redirect kembali ke dashboard admin dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil disetujui.');
    }

    /**
     * Menolak booking.
     */
    public function reject(Booking $booking)
    {
        // Ganti statusnya menjadi 'rejected'
        $booking->update(['status' => 'rejected']);

        // Redirect kembali ke dashboard admin dengan pesan sukses
        return redirect()->route('admin.dashboard')->with('success', 'Booking berhasil ditolak.');
    }
}