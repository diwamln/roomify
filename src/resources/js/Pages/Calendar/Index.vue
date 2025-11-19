<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel } from '@headlessui/vue';

const props = defineProps({
  events: Array
});

// ==========================================
// MINI CALENDAR LOGIC
// ==========================================
const date = new Date();
const currentMonth = ref(date.getMonth());
const currentYear = ref(date.getFullYear());
const todayDate = date.getDate();
const todayMonth = date.getMonth();
const todayYear = date.getFullYear();

const monthNames = [
    "January", "February", "March", "April", "May", "June", 
    "July", "August", "September", "October", "November", "December"
];

const miniCalendarTitle = computed(() => {
    return `${monthNames[currentMonth.value]} ${currentYear.value}`;
});

const calendarDays = computed(() => {
    const days = [];
    const firstDayOfMonth = new Date(currentYear.value, currentMonth.value, 1).getDay();
    const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear.value, currentMonth.value, 0).getDate();

    for (let i = firstDayOfMonth; i > 0; i--) {
        days.push({ date: daysInPrevMonth - i + 1, type: 'prev' });
    }

    for (let i = 1; i <= daysInMonth; i++) {
        const isToday = i === todayDate && currentMonth.value === todayMonth && currentYear.value === todayYear;
        days.push({ date: i, type: 'current', isToday });
    }

    const remainingCells = 42 - days.length;
    for (let i = 1; i <= remainingCells; i++) {
        days.push({ date: i, type: 'next' });
    }

    return days;
});

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

// ==========================================
// MODAL STATE
// ==========================================
const showModal = ref(false);
const selectedEvent = ref(null);

const handleEventClick = (info) => {
    selectedEvent.value = {
        title: info.event.title,
        start: info.event.start,
        end: info.event.end,
        ...info.event.extendedProps
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => selectedEvent.value = null, 200);
};

// ==========================================
// FULLCALENDAR CONFIG
// ==========================================
const calendarOptions = ref({
  plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin ],
  initialView: 'timeGridWeek',
  
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' 
  },

  buttonText: {
    today: 'Hari Ini',
    month: 'Bulan',
    week: 'Minggu',
    day: 'Hari',
    list: 'List'
  },

  titleFormat: { year: 'numeric', month: 'long' },
  slotMinTime: '07:00:00',
  slotMaxTime: '18:00:00',
  allDaySlot: false,
  nowIndicator: true,
  expandRows: true,
  height: '100%',
  slotDuration: '01:00:00',
  
  events: props.events,
  editable: false,
  selectable: false,
  eventClick: handleEventClick,

  dayHeaderContent: (args) => {
    const dayName = args.date.toLocaleDateString('id-ID', { weekday: 'short' }).toUpperCase();
    const dayNum = args.date.getDate();
    
    return {
      html: `
        <div class="flex flex-col items-center py-3 px-2">
            <span class="text-[10px] font-semibold text-gray-400 tracking-wider mb-1">
                ${dayName}
            </span>
            <span class="text-2xl font-semibold text-gray-800">
                ${dayNum}
            </span>
        </div>
      `
    }
  },

  // --- BAGIAN INI YANG DIPERBAIKI AGAR TAMPILAN LEBIH LENGKAP ---
  eventContent: function(arg) {
    const status = arg.event.extendedProps.status;
    const bgColor = status === 'approved' ? 'bg-emerald-500' : 'bg-yellow-500';
    // Ambil judul kegiatan (memisahkan dari nama ruangan jika ada separator ' - ')
    const activityTitle = arg.event.title.includes(' - ') 
        ? arg.event.title.split(' - ')[1] 
        : arg.event.title;

    return {
      html: `
        <div class="flex flex-col justify-between p-1.5 h-full w-full overflow-hidden ${bgColor} rounded shadow-sm hover:shadow-md transition-all border-l-4 border-black/10">
          
          <!-- Header: Ruangan & Jam -->
          <div class="flex justify-between items-start mb-1">
             <span class="text-[9px] font-bold text-white uppercase tracking-wider bg-black/20 px-1.5 py-0.5 rounded">
                ${arg.event.extendedProps.room || 'Room'}
             </span>
             ${arg.timeText ? `<span class="text-[10px] font-medium text-white whitespace-nowrap ml-1">${arg.timeText}</span>` : ''}
          </div>
          
          <!-- Body: Judul Kegiatan -->
          <div class="text-xs font-bold text-white leading-snug line-clamp-2 mb-1">
             ${activityTitle}
          </div>

          <!-- Footer: Organizer -->
          <div class="text-[10px] text-white/90 truncate flex items-center gap-1 mt-auto pt-1 border-t border-white/20">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-80" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
             </svg>
             <span class="truncate">${arg.event.extendedProps.organizer || 'Organizer'}</span>
          </div>

        </div>
      `
    }
  }
});
</script>

<style>
/* ==========================================
   CUSTOM FULLCALENDAR STYLING
   ========================================== */
.fc { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    height: 100%;
}

.fc-theme-standard td, 
.fc-theme-standard th { 
    border-color: #E5E7EB !important;
}

/* Toolbar */
.fc .fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.01em;
}

.fc .fc-toolbar-chunk {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.fc .fc-button {
    background-color: #FFFFFF !important;
    border: 1px solid #D1D5DB !important;
    color: #374151 !important;
    font-size: 0.8rem !important;
    padding: 0.4rem 0.75rem !important;
    font-weight: 500 !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    border-radius: 6px !important;
    transition: all 0.15s ease !important;
}

.fc .fc-button:hover { 
    background-color: #F9FAFB !important;
    border-color: #9CA3AF !important;
}

.fc .fc-button-active {
    background-color: #3B82F6 !important;
    color: #FFFFFF !important;
    border-color: #3B82F6 !important;
}

/* Column Headers */
.fc .fc-col-header-cell {
    background-color: #F9FAFB;
    border-bottom: 2px solid #E5E7EB !important;
    padding: 0 !important;
}

/* Today Highlight */
.fc-day-today {
    background-color: #EFF6FF !important;
}

.fc-day-today .fc-col-header-cell-cushion .fc-day-number { 
    color: #3B82F6 !important;
    font-weight: 700 !important;
}

/* Time Slots */
.fc-timegrid-slot {
    height: 3.5rem !important;
}

.fc-timegrid-slot-label-cushion { 
    font-size: 11px;
    color: #6B7280;
    font-weight: 500;
}

.fc .fc-timegrid-axis {
    background-color: #FAFAFA;
}

/* Events */
.fc-event {
    border: none !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    background: transparent !important; /* Penting agar bg custom terlihat */
}

.fc-event:hover {
    transform: translateY(-1px);
    /* box-shadow handled by inner div */
}

/* Now Indicator */
.fc .fc-timegrid-now-indicator-line {
    border-color: #EF4444 !important;
    border-width: 2px !important;
}

.fc .fc-timegrid-now-indicator-arrow {
    border-color: #EF4444 !important;
}

/* List View */
.fc-list-event:hover td {
    background-color: #F9FAFB !important;
}

/* Scrollbar */
.fc-scroller::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.fc-scroller::-webkit-scrollbar-track {
    background: #F3F4F6;
}

.fc-scroller::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 4px;
}

.fc-scroller::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}
</style>

<template>
  <Head title="Kalender Kampus" />

  <AuthenticatedLayout>
    <div class="bg-gray-50 py-6 w-full">
      <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200" style="height: calc(100vh - 140px);">
        
          <!-- SIDEBAR -->
          <div class="w-72 flex-shrink-0 border-r border-gray-200 bg-white py-6 px-5 hidden lg:flex flex-col gap-6">
            
            <!-- Header -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Kalender Kampus</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Jadwal penggunaan ruangan yang telah disetujui.</p>
            </div>

            <!-- Mini Calendar -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-bold text-gray-800">{{ miniCalendarTitle }}</span>
                    <div class="flex gap-1">
                        <button @click="prevMonth" class="p-1 hover:bg-gray-200 rounded transition">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button @click="nextMonth" class="p-1 hover:bg-gray-200 rounded transition">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-medium text-gray-500 mb-2">
                    <span>M</span><span>S</span><span>S</span><span>R</span><span>K</span><span>J</span><span>S</span>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    <span 
                        v-for="(day, index) in calendarDays" 
                        :key="index"
                        class="p-1.5 rounded-lg cursor-pointer flex items-center justify-center h-8 w-8 mx-auto transition-all duration-150"
                        :class="{
                            'text-gray-300': day.type !== 'current',
                            'bg-indigo-600 text-white font-bold shadow-md hover:bg-indigo-700': day.isToday,
                            'hover:bg-gray-200': !day.isToday && day.type === 'current',
                            'text-gray-700 font-medium': day.type === 'current' && !day.isToday
                        }"
                    >
                        {{ day.date }}
                    </span>
                </div>
            </div>

            <!-- Legend -->
            <div>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Status Booking</h4>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 group">
                        <div class="w-4 h-4 rounded bg-emerald-500 shadow-sm group-hover:scale-110 transition-transform"></div>
                        <span class="text-sm text-gray-700 font-medium">Disetujui</span>
                    </div>
                    <div class="flex items-center gap-3 group">
                        <div class="w-4 h-4 rounded bg-yellow-500 shadow-sm group-hover:scale-110 transition-transform"></div>
                        <span class="text-sm text-gray-700 font-medium">Menunggu Approval</span>
                    </div>
                </div>
            </div>
          </div>

          <!-- MAIN CALENDAR -->
          <div class="flex-1 bg-white overflow-hidden">
              <div class="h-full p-6"> 
                  <FullCalendar :options="calendarOptions" class="h-full w-full" />
              </div>
          </div>

        </div>
      </div>
    </div>

    <!-- MODAL DETAIL EVENT -->
    <TransitionRoot appear :show="showModal" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-50">
        <TransitionChild 
            as="template" 
            enter="duration-300 ease-out" 
            enter-from="opacity-0" 
            enter-to="opacity-100" 
            leave="duration-200 ease-in" 
            leave-from="opacity-100" 
            leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4">
            <TransitionChild 
                as="template" 
                enter="duration-300 ease-out" 
                enter-from="opacity-0 scale-95" 
                enter-to="opacity-100 scale-100" 
                leave="duration-200 ease-in" 
                leave-from="opacity-100 scale-100" 
                leave-to="opacity-0 scale-95"
            >
              <DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all">
                
                <!-- Header -->
                <div class="px-6 py-5 flex justify-between items-start" 
                     :class="selectedEvent?.status === 'approved' ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' : 'bg-gradient-to-r from-yellow-500 to-yellow-600'">
                    <div>
                        <h3 class="text-xl font-bold text-white leading-6 mb-1">
                          {{ selectedEvent?.title?.split(' - ')[1] || selectedEvent?.title }}
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white/20 text-white">
                            {{ selectedEvent?.status === 'approved' ? '✓ Disetujui' : '⏱ Menunggu' }}
                        </span>
                    </div>
                    <button @click="closeModal" class="text-white/90 hover:text-white transition p-1 hover:bg-white/10 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1">Ruangan</p>
                            <p class="text-gray-900 font-semibold text-base">{{ selectedEvent?.room }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1">Waktu</p>
                            <p class="text-gray-900 font-semibold text-base">{{ selectedEvent?.time_desc }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="bg-purple-50 p-3 rounded-xl text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-1">Peminjam</p>
                            <p class="text-gray-900 font-semibold text-base">{{ selectedEvent?.organizer }}</p>
                        </div>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-200">
                         <p class="text-xs text-gray-500 uppercase font-bold tracking-wide mb-2">Deskripsi Kegiatan</p>
                         <p class="text-sm text-gray-700 leading-relaxed">{{ selectedEvent?.description || 'Tidak ada deskripsi.' }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button @click="closeModal" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 hover:bg-gray-200 rounded-lg transition">
                        Tutup
                    </button>
                </div>

              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

  </AuthenticatedLayout>
</template>