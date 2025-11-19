<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama
        $totalBookings = Booking::count();
        $activeRooms = Room::count();
        $pendingApprovals = Booking::where('status', 'pending')->count();
        $approvedCount = Booking::where('status', 'approved')->count();
        
        // Hitung Usage Rate sederhana (Approved / Total * 100)
        $usageRate = $totalBookings > 0 ? round(($approvedCount / $totalBookings) * 100) . '%' : '0%';

        // 2. Upcoming Bookings (3 terdekat yang disetujui/pending dan belum lewat)
        $upcomingBookings = Booking::with('room')
            ->where('start_time', '>', now())
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('start_time', 'asc')
            ->take(3)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'room' => $booking->room->name,
                    'date' => \Carbon\Carbon::parse($booking->start_time)->format('d M Y'),
                    'time' => \Carbon\Carbon::parse($booking->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($booking->end_time)->format('H:i'),
                    'status' => $booking->status,
                    'purpose' => $booking->title, // Asumsi kolom 'title' adalah tujuan
                ];
            });

        // 3. Popular Rooms (Ruangan dengan booking terbanyak)
        $popularRooms = Room::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(4)
            ->get()
            ->map(function ($room) {
                return [
                    'name' => $room->name,
                    'bookings' => $room->bookings_count,
                    'capacity' => $room->capacity,
                    // Kita set gambar default berdasarkan nama atau random icon
                    'image' => '/iconDasboard/laptop.png' // Default icon
                ];
            });

        // 4. Recent Activity (5 Booking terakhir yang dibuat)
        $recentActivity = Booking::with(['user', 'room'])
            ->latest() // Urutkan dari yang terbaru dibuat
            ->take(4)
            ->get()
            ->map(function ($booking) {
                return [
                    'action' => 'New Booking', // Sederhana dulu
                    'room' => $booking->room->name,
                    'time' => $booking->created_at->diffForHumans(), // Contoh: "2 hours ago"
                    'user' => $booking->user->name,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalBookings,
                'rooms' => $activeRooms,
                'pending' => $pendingApprovals,
                'usage' => $usageRate,
            ],
            'upcoming' => $upcomingBookings,
            'popular' => $popularRooms,
            'activity' => $recentActivity,
        ]);
    }
}