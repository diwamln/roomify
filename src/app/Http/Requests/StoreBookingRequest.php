<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Booking;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Izinkan semua user yang login
    }

    public function rules(): array
    {
        // Ambil booking ID jika ini proses update (untuk exclude diri sendiri saat cek bentrok)
        $bookingId = $this->route('booking') ? $this->route('booking')->id : null;

        return [
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => [
                'required',
                'date',
                'after:start_time',
                // Custom Rule: Cek Bentrok
                function ($attribute, $value, $fail) use ($bookingId) {
                    $start = $this->start_time;
                    $end = $value;
                    $roomId = $this->room_id;

                    $isConflict = Booking::where('room_id', $roomId)
                        ->where('status', '!=', 'rejected') // Abaikan yang ditolak
                        ->when($bookingId, function ($q) use ($bookingId) {
                            $q->where('id', '!=', $bookingId); // Abaikan booking ini sendiri saat edit
                        })
                        ->where(function ($query) use ($start, $end) {
                            // Logika Overlap Waktu
                            $query->whereBetween('start_time', [$start, $end])
                                  ->orWhereBetween('end_time', [$start, $end])
                                  ->orWhere(function ($q) use ($start, $end) {
                                      $q->where('start_time', '<', $start)
                                        ->where('end_time', '>', $end);
                                  });
                        })
                        ->exists();

                    if ($isConflict) {
                        $fail('Jadwal bentrok! Ruangan ini sudah terisi pada jam tersebut.');
                    }
                },
            ],
        ];
    }
}