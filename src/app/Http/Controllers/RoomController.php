<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Menampilkan daftar semua ruangan.
     */
    public function index()
    {
        $rooms = Room::latest()->paginate(10);
        return view('rooms.index', compact('rooms'));
    }

    /**
     * Menampilkan form untuk membuat ruangan baru.
     */
    public function create()
    {
        return view('rooms.create');
    }

    /**
     * Menyimpan ruangan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
        ]);

        Room::create($request->all());

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu ruangan (opsional).
     */
    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    /**
     * Menampilkan form untuk mengedit ruangan.
     */
    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    /**
     * Mengupdate data ruangan di database.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
        ]);

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Data ruangan berhasil diperbarui.');
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