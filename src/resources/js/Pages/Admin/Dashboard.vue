<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import MonthlyChart from '@/Components/Charts/MonthlyChart.vue';

// Definisi Props Lengkap
const props = defineProps({
    stats: { 
        type: Object, 
        default: () => ({ total: 0, rooms: 0, pending: 0, usage: '0%' }) 
    },
    upcoming: { 
        type: Array, 
        default: () => [] 
    },
    popular: { 
        type: Array, 
        default: () => [] 
    },
    activity: { 
        type: Array, 
        default: () => [] 
    },
    chartMonthly: { 
        type: Array, 
        default: () => [] 
    },
    chartStatus: { 
        type: Array, 
        default: () => [] 
    }
});

// Logic Statistik Kartu Atas
const statsCards = computed(() => [
    { 
        title: 'Total Bookings', 
        value: props.stats?.total || 0, 
        change: '+12%', 
        trend: 'up', 
        icon: '/iconDasboard/calendar.png' 
    },
    { 
        title: 'Active Rooms', 
        value: props.stats?.rooms || 0, 
        change: '+0', 
        trend: 'up', 
        icon: '/iconDasboard/group.png' 
    },
    { 
        title: 'Pending Approval', 
        value: props.stats?.pending || 0, 
        change: 'Urgent', 
        trend: (props.stats?.pending || 0) > 0 ? 'down' : 'up', 
        icon: '/iconDasboard/hourglass.png' 
    },
    { 
        title: 'Usage Rate', 
        value: props.stats?.usage || '0%', 
        change: 'Active', 
        trend: 'up', 
        icon: '/iconDasboard/statistics.png' 
    }
]);

// Helper Icon untuk Popular Rooms
const getRoomIcon = (index) => {
    const icons = [
        '/iconDasboard/laptop.png', 
        '/iconDasboard/book.png', 
        '/iconDasboard/mask.png', 
        '/iconDasboard/meeting.png'
    ];
    return icons[index % icons.length];
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold leading-tight text-gray-800">
                    Admin Dashboard
                </h2>
                <div class="flex items-center gap-2">
                    <!-- Tombol Export Laporan -->
                    <a 
                        :href="route('reports.export')" 
                        class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition font-medium flex items-center gap-2 shadow-sm text-sm"
                    >
                        Export Report
                    </a>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
                
                <!-- Welcome Section -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border-2 border-gray-200">
                    <h1 class="text-2xl font-bold mb-1 text-gray-800">
                        Halo Admin, {{ $page.props.auth.user.name }}!
                    </h1>
                    <p class="text-sm text-gray-600">
                        Pantau aktivitas ruangan dan persetujuan booking kampus hari ini.
                    </p>
                </div>

                <!-- 1. Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div 
                        v-for="stat in statsCards" 
                        :key="stat.title"
                        class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4 flex items-start justify-between hover:border-indigo-300 transition-all"
                    >
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold mb-1">{{ stat.title }}</p>
                            <div class="flex items-end gap-2">
                                <h3 class="text-2xl font-bold text-gray-900 leading-none">{{ stat.value }}</h3>
                                <span 
                                    :class="stat.trend === 'up' ? 'text-green-600' : 'text-red-600'"
                                    class="text-xs font-bold mb-0.5"
                                >
                                    {{ stat.change }}
                                </span>
                            </div>
                        </div>
                        <img :src="stat.icon" class="w-8 h-8 object-contain opacity-80" :alt="stat.title" />
                    </div>
                </div>

                <!-- 2. AREA CHART (GRAFIK) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Grafik Utama (2/3 layar) -->
                    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Tren Booking Tahun Ini</h3>
                                <p class="text-sm text-gray-500">Grafik aktivitas peminjaman ruangan per bulan</p>
                            </div>
                            <div class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600 font-medium">
                                {{ new Date().getFullYear() }}
                            </div>
                        </div>
                        
                        <!-- Komponen Chart -->
                        <MonthlyChart :data="chartMonthly" />
                        
                    </div>

                    <!-- Ringkasan Status (1/3 layar) -->
                    <div class="bg-indigo-900 text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 bg-white opacity-10 rounded-full"></div>

                        <div>
                            <h3 class="text-lg font-bold mb-1">Status Booking</h3>
                            <p class="text-indigo-200 text-sm">Ringkasan status saat ini</p>
                        </div>

                        <div class="space-y-4 mt-6 relative z-10">
                            <!-- Status Approved -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium">Disetujui</span>
                                    <span class="font-bold">{{ chartStatus[0] || 0 }}</span>
                                </div>
                                <div class="w-full bg-indigo-800 rounded-full h-2">
                                    <div class="bg-green-400 h-2 rounded-full" :style="`width: ${(chartStatus[0] / (stats.total || 1)) * 100}%`"></div>
                                </div>
                            </div>

                            <!-- Status Pending -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium">Menunggu</span>
                                    <span class="font-bold">{{ chartStatus[1] || 0 }}</span>
                                </div>
                                <div class="w-full bg-indigo-800 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" :style="`width: ${(chartStatus[1] / (stats.total || 1)) * 100}%`"></div>
                                </div>
                            </div>

                            <!-- Status Rejected -->
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium">Ditolak</span>
                                    <span class="font-bold">{{ chartStatus[2] || 0 }}</span>
                                </div>
                                <div class="w-full bg-indigo-800 rounded-full h-2">
                                    <div class="bg-red-400 h-2 rounded-full" :style="`width: ${(chartStatus[2] / (stats.total || 1)) * 100}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    
                    <!-- 3. Upcoming Bookings -->
                    <div class="lg:col-span-2 bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                Upcoming Bookings
                            </h3>
                            <Link :href="route('admin.bookings.index')" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View All →</Link>
                        </div>
                        <div class="space-y-3">
                            <div 
                                v-for="booking in upcoming" 
                                :key="booking.id"
                                class="bg-white border-2 border-gray-200 rounded-xl p-3 hover:shadow-md transition-all hover:border-indigo-300"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-sm text-gray-900">{{ booking.room }}</h4>
                                            <span 
                                                :class="booking.status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                                class="text-[10px] font-semibold px-2 py-0.5 rounded-full uppercase">
                                                {{ booking.status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-1">{{ booking.purpose }}</p>
                                        <div class="flex items-center gap-3 text-[10px] text-gray-500">
                                            <span class="flex items-center gap-1">📅 {{ booking.date }}
                                            </span>
                                            <span class="flex items-center gap-1">⏰{{ booking.time }}
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Tombol Aksi Cepat Admin -->
                                    <Link :href="route('admin.bookings.index')" class="text-gray-400 hover:text-gray-600">
                                        <img src="/iconDasboard/menu.png" class="w-4 h-4 opacity-50" alt="Details" />
                                    </Link>
                                </div>
                            </div>
                            <div v-if="upcoming.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                Belum ada booking mendatang.
                            </div>
                        </div>
                    </div>

                    <!-- 4. Quick Actions (DISESUAIKAN UNTUK ADMIN) -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            Admin Actions
                        </h3>
                        <div class="space-y-2">
                            <!-- Tombol 1: Manajemen Booking -->
                            <Link :href="route('admin.bookings.index')" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-3 hover:bg-gray-50 hover:border-gray-300 transition-all text-left font-medium shadow-sm group flex items-center gap-2">
                                <img src="/iconDasboard/show.png" class="w-5 h-5 object-contain opacity-60 group-hover:opacity-80 transition-opacity" alt="" />
                                <span class="text-sm">Manajemen Booking</span>
                            </Link>

                            <!-- Tombol 2: Manajemen Ruangan -->
                            <Link :href="route('rooms.index')" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-3 hover:bg-gray-50 hover:border-gray-300 transition-all text-left font-medium shadow-sm group flex items-center gap-2">
                                <img src="/iconDasboard/file.png" class="w-5 h-5 object-contain opacity-60 group-hover:opacity-80 transition-opacity" alt="" />
                                <span class="text-sm">Manajemen Ruangan</span>
                            </Link>

                            <!-- Tombol 3: Settings -->
                            <Link :href="route('profile.edit')" class="w-full bg-white border border-gray-200 text-gray-700 rounded-xl p-3 hover:bg-gray-50 hover:border-gray-300 transition-all text-left font-medium shadow-sm group flex items-center gap-2">
                                <img src="/iconDasboard/setting.png" class="w-5 h-5 object-contain opacity-60 group-hover:opacity-80 transition-opacity" alt="" />
                                <span class="text-sm">Settings</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    
                    <!-- 5. Popular Rooms -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                            Popular Rooms
                        </h3>
                        <div class="space-y-3">
                            <div 
                                v-for="(room, index) in popular" 
                                :key="room.name"
                                class="bg-white border-2 border-gray-200 rounded-xl p-3 hover:shadow-md transition-all hover:border-indigo-300"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="p-1.5 bg-gray-50 rounded-lg">
                                        <img :src="getRoomIcon(index)" class="w-8 h-8 object-contain" :alt="room.name" />
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-bold text-sm text-gray-900">{{ room.name }}</h4>
                                            <span class="text-[10px] font-semibold text-gray-500">#{{ index + 1 }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600">{{ room.bookings }} bookings</span>
                                            <span class="text-gray-500">Capacity: {{ room.capacity }}</span>
                                        </div>
                                        <div class="mt-1.5 bg-gray-200 rounded-full h-1.5">
                                            <div 
                                                class="bg-indigo-600 rounded-full h-1.5 transition-all"
                                                :style="{ width: Math.min((room.bookings / 20 * 100), 100) + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="popular.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                Belum ada data ruangan.
                            </div>
                        </div>
                    </div>

                    <!-- 6. Recent Activity -->
                    <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-2xl p-4">
                        <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                             Recent Activity
                        </h3>
                        <div class="space-y-3">
                            <div 
                                v-for="(item, index) in activity" 
                                :key="index"
                                class="flex items-start gap-3 pb-3 border-b-2 border-gray-100 last:border-0"
                            >
                                <div class="bg-indigo-100 rounded-full p-1.5">
                                    <img src="/iconDasboard/notification-bell.png" class="w-3 h-3" alt="Notif" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ item.action }}</p>
                                    <p class="text-xs text-gray-600">{{ item.room }} • {{ item.user }}</p>
                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ item.time }}</p>
                                </div>
                            </div>
                             <div v-if="activity.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                Belum ada aktivitas.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>