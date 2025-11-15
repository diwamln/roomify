<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; // Pastikan ini ada

class BookingController extends Controller
{
    /**
     * Menampilkan daftar booking milik user yang sedang login.
     */
    public function index()
    {
        $myBookings = Booking::where('user_id', Auth::id())
                            ->with('room') // Ambil data ruangan terkait
                            ->latest()
                            ->paginate(10);
        
        return view('bookings.index', compact('myBookings'));
    }

    /**
     * Menampilkan form untuk membuat booking baru.
     */
    public function create()
    {
        $rooms = Room::orderBy('name')->get(); // Ambil semua ruangan untuk dropdown
        return view('bookings.create', compact('rooms'));
    }

    /**
     * Menyimpan booking baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        // Cek jadwal bentrok
        $conflict = Booking::where('room_id', $request->room_id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($q) use ($request) {
                // Cek kondisi tumpang tindih
                $q->where(function ($query) use ($request) {
                    // Mulai di dalam rentang yang ada
                    $query->where('jam_mulai', '<=', $request->jam_mulai)
                          ->where('jam_selesai', '>', $request->jam_mulai);
                })->orWhere(function ($query) use ($request) {
                    // Selesai di dalam rentang yang ada
                    $query->where('jam_mulai', '<', $request->jam_selesai)
                          ->where('jam_selesai', '>=', $request->jam_selesai);
                })->orWhere(function ($query) use ($request) {
                    // Mencakup sepenuhnya rentang yang ada
                    $query->where('jam_mulai', '>=', $request->jam_mulai)
                          ->where('jam_selesai', '<=', $request->jam_selesai);
                });
            })->exists();

        if ($conflict) {
            // Kembali ke form dengan input lama dan pesan error
            return back()->withInput()->withErrors(['msg' => 'Jadwal pada ruangan dan jam tersebut sudah terisi.']);
        }

        Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status' => 'pending', // Role 'admin' nanti yang ganti
        ]);

        return redirect()->route('bookings.index')->with('success', 'Ruangan berhasil dibooking. Menunggu persetujuan Admin.');
    }
}