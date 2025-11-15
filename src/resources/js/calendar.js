import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from "@fullcalendar/interaction"; // <-- Kita butuh ini juga

// Kita tunggu sampai halaman siap
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    // Pastikan elemen kalender ada sebelum melanjutkan
    if (calendarEl) {
        var calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin], // Daftarkan plugin
            initialView: 'timeGridWeek', // Tampilan awal (mingguan)
            slotMinTime: '07:00:00', // Jam mulai (7 pagi)
            slotMaxTime: '21:00:00', // Jam selesai (9 malam)
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay' // Pilihan view
            },
            
            // events: [] // Kita akan isi ini di langkah selanjutnya
            // Untuk saat ini, kita gunakan data contoh
            events: '/api/bookings',
        });
        
        // Render kalendernya
        calendar.render();
    } else {
        console.error("Elemen #calendar tidak ditemukan.");
    }
});