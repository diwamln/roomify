<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker; // 🔥 WAJIB: Import Faker Factory

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. INISIALISASI FAKER LOKAL (untuk data dummy)
        $faker = \Faker\Factory::create('id_ID');

        // ==========================================
        // 2. BUAT USER INTI (Admin & Mahasiswa)
        // ==========================================
        // Admin Utama
        User::create([
            'name' => 'Admin Roomify',
            'email' => 'admin@roomify.com',
            'password' => Hash::make('password'), 
            'role' => 'admin',
        ]);

        // User Biasa (Mahasiswa)
        $student = User::create([
            'name' => 'Mahasiswa Teladan',
            'email' => 'mahasiswa@roomify.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 🔥 Catatan: User::factory(5)->create() dihilangkan karena memicu error.

        // ==========================================
        // 3. BUAT DATA RUANGAN (Rooms)
        // ==========================================
        $newRoomsData = [];

        // --- SAW & Pascasarjana ---
        $buildings_basic = ['SAW', 'Pascasarjana'];
        for ($gedung = 0; $gedung < count($buildings_basic); $gedung++) {
            for ($lantai = 1; $lantai <= 11; $lantai++) {
                for ($ruang = 1; $ruang <= 8; $ruang++) {
                    $roomName = $buildings_basic[$gedung] . ' Lantai ' . str_pad($lantai, 2, '0', STR_PAD_LEFT) . ' Ruangan ' . str_pad($ruang, 2, '0', STR_PAD_LEFT);
                    
                    $newRoomsData[] = [
                        'name' => $roomName, 
                        'gedung' => $buildings_basic[$gedung], 
                        'lantai' => $lantai, 
                        'capacity' => 40, 
                        'facilities' => 'AC, Proyektor, Whiteboard',
                        'status' => 'available' 
                    ];
                }
            }
        }

        // --- D3 & D4 (Ruangan Teori & Praktikum) ---
        $buildings_d = ['D3', 'D4'];
        
        for ($gedung = 0; $gedung < count($buildings_d); $gedung++) {
            for ($lantai = 1; $lantai <= 3; $lantai++) {
                $building_name = $buildings_d[$gedung];
                $lantai_padded = str_pad($lantai, 2, '0', STR_PAD_LEFT);
                
                $teori_char = match ($lantai) {
                    1 => 'A', 2 => 'B', 3 => 'C', default => 'X',
                };
                
                // Ruangan Teori 01 sampai 05
                for ($ruang = 1; $ruang <= 5; $ruang++) {
                    $roomName = $building_name . ' Lantai ' . $lantai_padded . ' Ruangan Teori ' . $teori_char . ' ' . str_pad($ruang, 2, '0', STR_PAD_LEFT);

                    $newRoomsData[] = [
                        'name' => $roomName, 'gedung' => $building_name, 'lantai' => $lantai, 
                        'capacity' => 30, 'facilities' => 'Kursi Kuliah, Proyektor, AC', 'status' => 'available'
                    ];
                }

                // Ruangan Praktikum (01 sampai 08)
                for ($ruang = 1; $ruang <= 8; $ruang++) {
                    $roomName = $building_name . ' Lantai ' . $lantai_padded . ' Ruangan Praktikum ' . str_pad($ruang, 2, '0', STR_PAD_LEFT);
                    
                    $newRoomsData[] = [
                        'name' => $roomName, 'gedung' => $building_name, 'lantai' => $lantai, 
                        'capacity' => 40, 'facilities' => 'PC Lab, AC, Whiteboard', 'status' => 'available'
                    ];
                }
            }
        }
        
        // Simpan semua data ruangan baru ke database
        foreach ($newRoomsData as $room) {
            Room::create($room);
        }

        // ==========================================
        // 4. BUAT DATA BOOKING DUMMY
        // ==========================================
        $rooms = Room::all(); 
        $users = User::all();
        $statuses = ['approved', 'pending', 'rejected', 'approved', 'approved'];

        // Generate 50 booking acak
        for ($i = 0; $i < 50; $i++) {
            $randomMonth = rand(1, 12);
            $randomDay = rand(1, 28);
            $randomHour = rand(8, 16);
            
            $start = Carbon::create(date('Y'), $randomMonth, $randomDay, $randomHour, 0, 0);
            $end = (clone $start)->addHours(rand(1, 3));

            Booking::create([
                'user_id' => $users->random()->id,
                'room_id' => $rooms->random()->id,
                'title' => 'Kegiatan ' . $faker->words(2, true), // Menggunakan $faker
                'description' => $faker->sentence(), // Menggunakan $faker
                'start_time' => $start,
                'end_time' => $end,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $start->subDays(rand(1, 5)),
            ]);
        }
        
        // Tambahkan booking spesifik untuk user demo
        Booking::create([
            'user_id' => $student->id,
            'room_id' => $rooms->random()->id, 
            'title' => 'Presentasi Project Akhir',
            'description' => 'Sidang skripsi tahap 1',
            'start_time' => now()->addDays(2)->setHour(10)->setMinute(0),
            'end_time' => now()->addDays(2)->setHour(12)->setMinute(0),
            'status' => 'approved',
        ]);
    }
}