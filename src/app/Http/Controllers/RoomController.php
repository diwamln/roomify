<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request; // Gunakan Request standar agar tidak perlu buat file Request terpisah
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Menampilkan daftar ruangan (Halaman Index)
     * DITINGKATKAN: Fitur Search & Pagination
     */
    public function index(Request $request)
    {
        $query = Room::query();

        // 1. Fitur Pencarian
        if ($request->input('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('gedung', 'like', "%{$search}%");
            });
        }

        // 2. Return Data dengan Pagination (10 data per halaman)
        // Mapping manual tidak perlu jika nama kolom DB sudah sesuai ('name', 'capacity', dst)
        return Inertia::render('Rooms/Index', [
            'rooms' => $query->orderBy('created_at', 'desc')
                             ->paginate(10)
                             ->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menampilkan form untuk membuat ruangan baru (Halaman Create).
     */
    public function create()
    {
        return Inertia::render('Rooms/Create');
    }

    /**
     * Menyimpan ruangan baru ke database.
     */
    public function store(Request $request) 
    {
        // Validasi input (termasuk gedung & lantai)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'gedung' => 'nullable|string|max:255',
            'lantai' => 'nullable|integer',
            'facilities' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit ruangan (Halaman Edit).
     */
    public function edit(Room $room)
    {
        return Inertia::render('Rooms/Edit', [
            'room' => $room 
        ]);
    }

    /**
     * Mengupdate data ruangan di database.
     */
    public function update(Request $request, Room $room) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'gedung' => 'nullable|string|max:255',
            'lantai' => 'nullable|integer',
            'facilities' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    /**
     * Menghapus ruangan dari database.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        
        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}