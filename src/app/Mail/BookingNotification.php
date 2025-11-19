<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status; // 'approved' atau 'rejected'

    public function __construct(Booking $booking, string $status)
    {
        $this->booking = $booking;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'Booking Disetujui: ' . $this->booking->title
            : 'Booking Ditolak: ' . $this->booking->title;

        return new Envelope(
            subject: '[Roomify] ' . $subject,
        );
    }

    public function content(): Content
    {
        // Kita gunakan view inline agar tidak perlu buat file blade terpisah dulu
        return new Content(
            htmlString: "
                <h1>Halo, {$this->booking->user->name}</h1>
                <p>Status pengajuan booking ruangan Anda telah diperbarui.</p>
                
                <p><strong>Kegiatan:</strong> {$this->booking->title}</p>
                <p><strong>Ruangan:</strong> {$this->booking->room->name}</p>
                <p><strong>Waktu:</strong> {$this->booking->start_time->format('d M Y H:i')}</p>
                
                <p>Status saat ini: <strong style='color: ".($this->status == 'approved' ? 'green' : 'red')."'>".strtoupper($this->status)."</strong></p>
                
                <br>
                <p>Terima kasih,<br>Admin Roomify</p>
            "
        );
    }
}