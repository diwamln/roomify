<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
// use App\Mail\BookingNotification; // Uncomment jika file Mail sudah ada

class BookingController extends Controller
{
    /**
     * ===============================================
     * METHOD UNTUK USER BIASA (Mahasiswa/Dosen)
     * ===============================================
     */

    /**
     * Menampilkan daftar booking milik user (Support Search & Pagination).
     */
    public function index(Request $request)
    {
        $query = Booking::with('room')
            ->where('user_id', Auth::id()) // Hanya data milik sendiri
            ->latest(); // Urutkan dari yang terbaru

        // Logika Pencarian
        if ($request->input('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('room', function($r) use ($search) {
                      $r->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return Inertia::render('Bookings/Index', [
            'bookings' => $query->paginate(10)->withQueryString(), // Pagination 10 item
            'filters' => $request->only(['search']), // Kirim state search ke frontend
        ]);
    }

    /**
     * Form booking baru.
     */
    public function create()
    {
        return Inertia::render('Bookings/Create', [
            'rooms' => Room::all(['id', 'name', 'capacity', 'gedung', 'lantai']),
        ]);
    }

    /**
     * Simpan booking baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input & Anti Bentrok
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => [
                'required', 
                'date', 
                'after:start_time',
                // LOGIC ANTI BENTROK (Saat Create)
                function ($attribute, $value, $fail) use ($request) {
                    $bentrok = Booking::where('room_id', $request->room_id)
                        ->where('status', '!=', 'rejected') // Abaikan yang ditolak
                        ->where(function ($query) use ($request) {
                            $query->where(function ($q) use ($request) {
                                $q->where('start_time', '<', $request->end_time)
                                  ->where('end_time', '>', $request->start_time);
                            });
                        })
                        ->exists();

                    if ($bentrok) {
                        $fail('Jadwal bentrok! Ruangan ini sudah dibooking orang lain pada jam tersebut.');
                    }
                },
            ],
        ]);

        // 2. Tambahkan User ID & Create
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending'; // Default status

        Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil diajukan. Menunggu persetujuan admin.');
    }

    /**
     * Form edit booking.
     */
    public function edit(Booking $booking)
    {
        // Cek Kepemilikan
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek Status (Hanya pending yang boleh diedit)
        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah diproses tidak dapat diedit.');
        }

        return Inertia::render('Bookings/Edit', [
            'booking' => $booking,
            // PERBAIKAN DI SINI: Menambahkan 'capacity', 'gedung', 'lantai'
            // Agar dropdown di Edit.vue bisa mendeteksi lokasi ruangan lama
            'rooms' => Room::all(['id', 'name', 'capacity', 'gedung', 'lantai']),
        ]);
    }
    /**
     * Update booking.
     */
    public function update(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking yang sudah diproses tidak dapat diedit.');
        }

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => [
                'required', 
                'date', 
                'after:start_time',
                // LOGIC ANTI BENTROK (Saat Update)
                function ($attribute, $value, $fail) use ($request, $booking) {
                    $bentrok = Booking::where('room_id', $request->room_id)
                        ->where('id', '!=', $booking->id) // Kecualikan diri sendiri
                        ->where('status', '!=', 'rejected')
                        ->where(function ($query) use ($request) {
                            $query->where(function ($q) use ($request) {
                                $q->where('start_time', '<', $request->end_time)
                                  ->where('end_time', '>', $request->start_time);
                            });
                        })
                        ->exists();

                    if ($bentrok) {
                        $fail('Jadwal bentrok dengan booking lain!');
                    }
                },
            ],
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil diperbarui.');
    }

    /**
     * Hapus / Batalkan Booking (User).
     */
    public function destroy(Booking $booking)
    {
        // Pastikan pemilik atau admin
        if ($booking->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Hanya bisa hapus jika pending (opsional, tergantung kebijakan)
        if ($booking->status !== 'pending' && auth()->user()->role !== 'admin') {
             return back()->with('error', 'Hanya booking pending yang bisa dibatalkan.');
        }

        $booking->delete();

        return redirect()->back()->with('success', 'Booking berhasil dibatalkan/dihapus.');
    }

    /**
     * ===============================================
     * METHOD KHUSUS UNTUK ADMIN
     * ===============================================
     */

    /**
     * Menampilkan SEMUA booking untuk Admin (Support Search & Pagination).
     */
    public function adminIndex(Request $request)
    {
        $query = Booking::with(['user', 'room'])
            ->latest();

        // Fitur Pencarian Admin (Cari Nama User, Room, atau Judul)
        if ($request->input('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function($r) use ($search) {
                      $r->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status (Optional)
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Menyetujui booking.
     */
    public function approve(Booking $booking)
    {
        $booking->update(['status' => 'approved']);
        
        // Kirim Email Notifikasi (Pastikan Mailtrap/SMTP sudah disetting di .env)
        // if ($booking->user && $booking->user->email) {
        //    Mail::to($booking->user->email)->send(new \App\Mail\BookingNotification($booking, 'approved'));
        // }

        return redirect()->back()->with('success', 'Booking berhasil disetujui.');
    }

    /**
     * Menolak booking.
     */
    public function reject(Booking $booking)
    {
        $booking->update(['status' => 'rejected']);
        
        // Kirim Email Notifikasi
        // if ($booking->user && $booking->user->email) {
        //    Mail::to($booking->user->email)->send(new \App\Mail\BookingNotification($booking, 'rejected'));
        // }

        return redirect()->back()->with('success', 'Booking berhasil ditolak.');
    }
}