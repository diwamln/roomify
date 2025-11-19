<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin
        User::create([
            'name' => 'Admin Roomify',
            'email' => 'admin@roomify.com',
            'password' => Hash::make('password'), // password
            'role' => 'admin',
        ]);

        // 2. Buat User Biasa (Mahasiswa)
        $student = User::create([
            'name' => 'Mahasiswa Teladan',
            'email' => 'mahasiswa@roomify.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 3. Buat 5 User Dummy Tambahan
        User::factory(5)->create();

        // 4. Buat Data Ruangan (Rooms)
        $roomsData = [
            ['name' => 'Lab Komputer A', 'gedung' => 'Gedung Teknologi', 'lantai' => 2, 'capacity' => 40, 'facilities' => 'PC i7, AC, Proyektor'],
            ['name' => 'Lab Komputer B', 'gedung' => 'Gedung Teknologi', 'lantai' => 2, 'capacity' => 40, 'facilities' => 'PC i5, AC, Smart TV'],
            ['name' => 'Auditorium Utama', 'gedung' => 'Gedung Rektorat', 'lantai' => 1, 'capacity' => 300, 'facilities' => 'Sound System, Panggung, AC Central'],
            ['name' => 'Ruang Kelas 101', 'gedung' => 'Gedung Bisnis', 'lantai' => 1, 'capacity' => 30, 'facilities' => 'AC, Whiteboard'],
            ['name' => 'Ruang Kelas 102', 'gedung' => 'Gedung Bisnis', 'lantai' => 1, 'capacity' => 30, 'facilities' => 'AC, Whiteboard'],
            ['name' => 'Ruang Rapat Dosen', 'gedung' => 'Gedung Rektorat', 'lantai' => 3, 'capacity' => 15, 'facilities' => 'Meja Bundar, TV, AC'],
            ['name' => 'Studio Musik', 'gedung' => 'Gedung Seni', 'lantai' => 4, 'capacity' => 10, 'facilities' => 'Alat Musik, Peredam Suara'],
        ];

        foreach ($roomsData as $room) {
            Room::create($room);
        }

        // 5. Buat Data Booking Dummy (Agar Grafik Dashboard Terisi)
        $rooms = Room::all();
        $users = User::all();
        $statuses = ['approved', 'pending', 'rejected', 'approved', 'approved']; // Lebih banyak approved biar grafik bagus

        // Generate 50 booking acak di tahun ini
        for ($i = 0; $i < 50; $i++) {
            $randomMonth = rand(1, 12);
            $randomDay = rand(1, 28);
            $randomHour = rand(8, 16);
            
            $start = \Carbon\Carbon::create(date('Y'), $randomMonth, $randomDay, $randomHour, 0, 0);
            $end = (clone $start)->addHours(rand(1, 3));

            Booking::create([
                'user_id' => $users->random()->id,
                'room_id' => $rooms->random()->id,
                'title' => 'Kegiatan ' . fake()->words(2, true),
                'description' => fake()->sentence(),
                'start_time' => $start,
                'end_time' => $end,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $start->subDays(rand(1, 5)), // Dibuat beberapa hari sebelum acara
            ]);
        }
        
        // Tambahkan booking spesifik untuk user demo hari ini (Supaya muncul di Upcoming)
        Booking::create([
            'user_id' => $student->id,
            'room_id' => $rooms->first()->id,
            'title' => 'Presentasi Project Akhir',
            'description' => 'Sidang skripsi tahap 1',
            'start_time' => now()->addDays(2)->setHour(10)->setMinute(0),
            'end_time' => now()->addDays(2)->setHour(12)->setMinute(0),
            'status' => 'approved',
        ]);
    }
}