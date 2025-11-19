<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();

            // === PERUBAHAN DARI KODE ANDA ===

            // 1. Menambahkan 'title' untuk nama kegiatan (Rapat HIMA, Kelas Tambahan, dll)
            //    Model dan Controller kita sudah mengasumsikan ini ada.
            $table->string('title');
            $table->text('description')->nullable();

            // 2. Mengganti 'tanggal', 'jam_mulai', 'jam_selesai'
            //    dengan 'start_time' dan 'end_time' (tipe DATETIME).
            //    Ini JAUH lebih mudah untuk cek tabrakan jadwal.
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // ===================================

            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};