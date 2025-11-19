<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MonthlyChart from '@/Components/Charts/MonthlyChart.vue';

// Props dari Controller (Backend)
const props = defineProps({
    stats: { 
        type: Object, 
        default: () => ({ total: 0, rooms: 0, pending: 0, usage: '0%' }) 
    },
    upcoming: { type: Array, default: () => [] },
    popular: { type: Array, default: () => [] },
    activity: { type: Array, default: () => [] },
    chartMonthly: { type: Array, default: () => [] } // Data untuk Grafik
});

// Ambil Data User untuk Sapaan
const page = usePage();
const user = computed(() => page.props.auth.user);

// Logic Statistik Kartu Atas
const statsCards = computed(() => [
    { 
        title: 'Total Booking Saya', 
        value: props.stats?.total || 0, 
        change: 'History', 
        trend: 'up', 
        icon: '/iconDasboard/calendar.png' 
    },
    { 
        // 'rooms' di sini berisi jumlah 'Approved' khusus User
        title: 'Booking Disetujui', 
        value: props.stats?.rooms || 0, 
        change: 'Verified', 
        trend: 'up', 
        icon: '/iconDasboard/group.png' 
    },
    { 
        title: 'Menunggu Persetujuan', 
        value: props.stats?.pending || 0, 
        change: 'Pending', 
        trend: (props.stats?.pending || 0) > 0 ? 'down' : 'up', 
        icon: '/iconDasboard/hourglass.png' 
    },
    { 
        title: 'Success Rate', 
        value: props.stats?.usage || '0%', 
        change: 'Ratio', 
        trend: 'up', 
        icon: '/iconDasboard/statistics.png' 
    }
]);

// Helper Icon Ruangan
const getRoomIcon = (index) => {
    const icons = ['/iconDasboard/laptop.png', '/iconDasboard/book.png', '/iconDasboard/mask.png', '/iconDasboard/meeting.png'];
    return icons[index % icons.length];
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Header Halaman -->
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
                <div class="flex items-center gap-2">
                    <!-- Tombol Export -->
                    <a 
                        :href="route('reports.export')" 
                        class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition font-medium flex items-center gap-2 shadow-sm text-sm"
                    >
                        Export History
                    </a>

                    <!-- Tombol Booking Baru -->
                    <Link 
                        :href="route('bookings.create')"
                        class="px-3 py-1.5 bg-gray-900 text-white border border-transparent rounded-lg hover:bg-gray-800 transition font-medium flex items-center gap-2 shadow-md text-sm"
                    >
                        + Booking Ruangan
                    </Link>
                </div>
            </div>
        </template>

        <!-- KONTEN UTAMA DASHBOARD -->
        <div class="py-6">
            <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
                
                <!-- Welcome Section -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-gray-200">
                    <h1 class="text-2xl font-bold mb-1 text-gray-800">
                        Selamat Datang, {{ user.name }}!
                    </h1>
                    <p class="text-sm text-gray-600">
                        Lihat status pengajuan ruangan dan jadwal kegiatanmu di sini.
                    </p>
                </div>

                <!-- 1. Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="stat in statsCards" 
                        :key="stat.title"
                        class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4 flex items-start justify-between hover:shadow-md transition-all"
                    >
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-1">{{ stat.title }}</p>
                            <div class="flex items-end gap-2">
                                <h3 class="text-2xl font-bold text-gray-900 leading-none">{{ stat.value }}</h3>
                                <span 
                                    :class="stat.trend === 'up' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50'"
                                    class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                >
                                    {{ stat.change }}
                                </span>
                            </div>
                        </div>
                        <img :src="stat.icon" class="w-8 h-8 object-contain opacity-80" :alt="stat.title" />
                    </div>
                </div>

                <!-- 2. CHART & QUICK ACTIONS -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    
                    <!-- GRAFIK AKTIVITAS (User Personal) -->
                    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                         <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Aktivitas Booking Saya</h3>
                                <p class="text-sm text-gray-500">Frekuensi peminjaman ruangan tahun ini</p>
                            </div>
                        </div>
                        <!-- Chart Component -->
                        <MonthlyChart :data="chartMonthly" />
                    </div>

                    <!-- Aksi Cepat -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4 flex flex-col">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            Aksi Cepat
                        </h3>
                        <div class="space-y-2 flex-1">
                            <!-- Link ke Buat Booking -->
                            <Link :href="route('bookings.create')" class="w-full bg-gray-900 text-white border border-transparent rounded-xl p-3 hover:bg-gray-800 transition-all text-left font-medium shadow-md flex items-center gap-3 group">
                                <div class="bg-white/20 p-1.5 rounded-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                                <div>
                                    <span class="block text-sm font-bold">Booking Baru</span>
                                    <span class="block text-[10px] opacity-80 font-normal">Ajukan peminjaman ruangan</span>
                                </div>
                            </Link>

                            <!-- Link ke Riwayat -->
                            <Link :href="route('bookings.index')" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-3 hover:bg-gray-50 hover:border-gray-300 transition-all text-left font-medium shadow-sm group flex items-center gap-3">
                                <img src="/iconDasboard/show.png" class="w-8 h-8 object-contain opacity-60 group-hover:opacity-80 transition-opacity" />
                                <div>
                                    <span class="block text-sm font-bold">Riwayat Booking</span>
                                    <span class="block text-[10px] text-gray-500 font-normal">Cek status pengajuan</span>
                                </div>
                            </Link>

                            <!-- Link ke Kalender -->
                             <Link :href="route('calendar.index')" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-3 hover:bg-gray-50 hover:border-gray-300 transition-all text-left font-medium shadow-sm group flex items-center gap-3">
                                <img src="/iconDasboard/calendar.png" class="w-8 h-8 object-contain opacity-60 group-hover:opacity-80 transition-opacity" />
                                <div>
                                    <span class="block text-sm font-bold">Kalender Kampus</span>
                                    <span class="block text-[10px] text-gray-500 font-normal">Lihat ketersediaan</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- 3. UPCOMING & POPULAR -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    
                    <!-- Upcoming Bookings (Personal) -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-900">Booking Mendatang</h3>
                            <Link :href="route('bookings.index')" class="text-xs text-indigo-600 hover:underline">Lihat Semua</Link>
                        </div>
                        <div class="space-y-3">
                            <div 
                                v-for="booking in upcoming" 
                                :key="booking.id"
                                class="bg-white border border-gray-200 rounded-xl p-3 hover:shadow-sm transition-all flex items-center justify-between"
                            >
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-sm text-gray-900">{{ booking.room }}</h4>
                                        <span :class="booking.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">{{ booking.status }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 flex gap-3">
                                        <span>📅 {{ booking.date }}</span>
                                        <span>⏰ {{ booking.time }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="upcoming.length === 0" class="text-center py-6 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-xl">
                                Tidak ada jadwal mendatang.
                            </div>
                        </div>
                    </div>

                    <!-- Popular Rooms (Global Insight) -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-4">Ruangan Terpopuler</h3>
                        <div class="space-y-3">
                            <div 
                                v-for="(room, index) in popular" 
                                :key="room.name"
                                class="flex items-center gap-3 p-2 hover:bg-white rounded-lg transition-all">
                                <div class="p-1.5 bg-gray-50 rounded-lg border border-gray-100">
                                    <img :src="getRoomIcon(index)" class="w-6 h-6 object-contain" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between mb-1">
                                        <h4 class="font-bold text-sm text-gray-800">{{ room.name }}</h4>
                                        <span class="text-xs text-gray-500">{{ room.bookings }} kali</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-indigo-500 h-1.5 rounded-full" :style="{ width: Math.min((room.bookings / 20 * 100), 100) + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>