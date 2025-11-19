<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AdminBookingController;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response; // Penting untuk Export CSV
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- HALAMAN DEPAN (WELCOME) ---
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// --- DASHBOARD (LOGIC PUSAT UNTUK ADMIN & USER) ---
Route::get('/dashboard', function () {
    // 1. Statistik Utama
    $totalBookings = Booking::count();
    $activeRooms = Room::count();
    $pendingApprovals = Booking::where('status', 'pending')->count();
    $approvedCount = Booking::where('status', 'approved')->count();
    // Menghindari pembagian dengan nol
    $usageRate = $totalBookings > 0 ? round(($approvedCount / $totalBookings) * 100) . '%' : '0%';

    // 2. Upcoming Bookings (3 terdekat yang akan datang)
    $upcomingBookings = Booking::with('room')
        ->whereIn('status', ['approved', 'pending'])
        ->where('start_time', '>', now())
        ->orderBy('start_time', 'asc')
        ->take(3)
        ->get()
        ->map(function ($booking) {
            return [
                'id' => $booking->id,
                'room' => $booking->room->name,
                'date' => \Carbon\Carbon::parse($booking->start_time)->format('d M Y'),
                'time' => \Carbon\Carbon::parse($booking->start_time)->format('H:i'),
                'status' => $booking->status,
                'purpose' => $booking->title,
            ];
        });

    // 3. Popular Rooms
    $popularRooms = Room::withCount('bookings')
        ->orderBy('bookings_count', 'desc')
        ->take(4)
        ->get()
        ->map(function ($room) {
            return [
                'name' => $room->name,
                'bookings' => $room->bookings_count,
                'capacity' => $room->capacity,
                'image' => '/iconDasboard/laptop.png' // Gambar default
            ];
        });

    // 4. Recent Activity
    $recentActivity = Booking::with(['user', 'room'])
        ->latest()
        ->take(4)
        ->get()
        ->map(function ($booking) {
            return [
                'action' => 'New Booking',
                'room' => $booking->room->name,
                'time' => $booking->created_at->diffForHumans(),
                'user' => $booking->user->name,
            ];
        });

    // 5. DATA GRAFIK (Fixed for PostgreSQL)
    // Menggunakan EXTRACT(MONTH ...) menggantikan MONTH(...)
    $monthlyBookings = Booking::selectRaw('EXTRACT(MONTH FROM start_time) as month, COUNT(*) as count')
        ->whereYear('start_time', date('Y'))
        ->groupByRaw('month')
        ->orderBy('month')
        ->pluck('count', 'month')
        ->toArray();

    // Normalisasi Data Grafik (Isi 0 untuk bulan yang kosong)
    $chartDataCounts = [];
    for ($i = 1; $i <= 12; $i++) {
        $chartDataCounts[] = $monthlyBookings[$i] ?? 0;
    }

    // 6. DATA STATUS (Untuk Pie Chart/Progress Bar)
    $statusStats = [
        Booking::where('status', 'approved')->count(),
        Booking::where('status', 'pending')->count(),
        Booking::where('status', 'rejected')->count(),
    ];

    // Kumpulkan semua data
    $data = [
        'stats' => [
            'total' => $totalBookings,
            'rooms' => $activeRooms,
            'pending' => $pendingApprovals,
            'usage' => $usageRate
        ],
        'upcoming' => $upcomingBookings,
        'popular' => $popularRooms,
        'activity' => $recentActivity,
        'chartMonthly' => $chartDataCounts,
        'chartStatus' => $statusStats
    ];

    // --- LOGIKA PEMISAH TAMPILAN ---
    // Jika Admin -> Tampilkan Dashboard Admin
    if (auth()->user()->role === 'admin') {
        return Inertia::render('Admin/Dashboard', $data);
    }

    // Jika User Biasa -> Tampilkan Dashboard User
    return Inertia::render('Dashboard', $data);

})->middleware(['auth', 'verified'])->name('dashboard');


// --- GRUP ROUTE UNTUK USER LOGIN (SEMUA ROLE) ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Booking Routes (User Biasa)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Kalender (Akses Semua User)
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
});


// --- GRUP ROUTE KHUSUS ADMIN ---
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    
    // Manajemen Ruangan (CRUD Lengkap kecuali show)
    Route::resource('rooms', RoomController::class)->except(['show']);
    
    // Manajemen Booking (Approval)
    // Pastikan method 'adminIndex' ada di BookingController, atau gunakan Controller terpisah
    Route::get('/admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
    Route::patch('/admin/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('admin.bookings.approve');
    Route::patch('/admin/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('admin.bookings.reject');

    // Fitur Export Laporan CSV
    Route::get('/reports/export', function () {
        $fileName = 'roomify_report_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header Kolom CSV
            fputcsv($file, ['ID', 'User', 'Room', 'Title', 'Date', 'Start Time', 'End Time', 'Status']);

            // Ambil data per chunk agar hemat memori
            Booking::with(['user', 'room'])
                ->orderBy('created_at', 'desc')
                ->chunk(100, function ($bookings) use ($file) {
                    foreach ($bookings as $booking) {
                        fputcsv($file, [
                            $booking->id,
                            $booking->user->name,
                            $booking->room->name,
                            $booking->title,
                            $booking->start_time, // Format default timestamp
                            \Carbon\Carbon::parse($booking->start_time)->format('H:i'),
                            \Carbon\Carbon::parse($booking->end_time)->format('H:i'),
                            $booking->status
                        ]);
                    }
                });
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    })->name('reports.export');
});

require __DIR__.'/auth.php';