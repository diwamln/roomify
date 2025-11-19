<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // 1. Ambil semua booking yang 'pending' atau 'approved'
        $bookings = Booking::with(['room', 'user'])
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        // 2. Format data sesuai standar FullCalendar
        $events = $bookings->map(function ($booking) {
            
            // Hijau (#10B981) untuk Approved, Kuning (#F59E0B) untuk Pending
            $color = $booking->status === 'approved' ? '#10B981' : '#F59E0B';

            return [
                'id' => $booking->id,
                'title' => $booking->room->name . ' - ' . $booking->title,
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                
                // Data tambahan untuk Modal Detail
                'extendedProps' => [
                    'status' => $booking->status,
                    'organizer' => $booking->user->name,
                    'room' => $booking->room->name,
                    'description' => $booking->description ?? '-',
                    'time_desc' => Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i'),
                ]
            ];
        });

        // 3. Render ke 'Calendar/Index' (Sesuai struktur folder Anda)
        return Inertia::render('Calendar/Index', [
            'events' => $events
        ]);
    }
}