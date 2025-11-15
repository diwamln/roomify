<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking; // <-- Import model Booking

class BookingStatusNotification extends Notification
{
    use Queueable;

    public Booking $booking; // Properti untuk menyimpan data booking

    /**
     * Buat instance notifikasi baru.
     */
    public function __construct(Booking $booking)
    {
        // Saat notifikasi dibuat, kita simpan data booking-nya
        $this->booking = $booking;
    }

    /**
     * Tentukan channel pengiriman notifikasi (via email).
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Buat representasi email dari notifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // $notifiable adalah user yang akan menerima email
        $greeting = "Halo " . $notifiable->name . ",";
        $subject = "";
        $line1 = "";

        // Atur isi email berdasarkan status booking
        if ($this->booking->status == 'approved') {
            $subject = "Booking Anda Disetujui!";
            $line1 = "Kabar baik! Booking Anda untuk ruangan '" . $this->booking->room->name . "' pada tanggal " . $this->booking->tanggal . " (" . $this->booking->jam_mulai . " - " . $this->booking->jam_selesai . ") telah disetujui.";
        } else { // Asumsi jika bukan 'approved' maka 'rejected'
            $subject = "Booking Anda Ditolak";
            $line1 = "Mohon maaf, booking Anda untuk ruangan '" . $this->booking->room->name . "' pada tanggal " . $this->booking->tanggal . " (" . $this->booking->jam_mulai . " - " . $this->booking->jam_selesai . ") telah ditolak.";
        }

        // Buat dan kembalikan emailnya
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->action('Lihat Booking Saya', route('bookings.index')) // Tombol di email
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }
}