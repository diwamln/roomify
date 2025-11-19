<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted, nextTick } from 'vue'; // Import nextTick

const props = defineProps({
    booking: Object, 
    rooms: Array     
});

const formatDateInput = (dateString) => {
    if (!dateString) return '';
    // Pastikan timezone lokal (Asia/Jakarta) tidak bergeser ke UTC
    const date = new Date(dateString);
    const offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset).toISOString().split('T')[0];
};

const formatTimeInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toTimeString().slice(0, 5);
};

const form = useForm({
    room_id: props.booking.room_id,
    title: props.booking.title,
    description: props.booking.description,
    date: formatDateInput(props.booking.start_time),
    start_time: formatTimeInput(props.booking.start_time),
    end_time: formatTimeInput(props.booking.end_time),
});

// === LOGIKA DROPDOWN BERTINGKAT ===
const selectedGedung = ref(null);
const selectedLantai = ref(null);
const isInitializing = ref(true); // FLAG PENTING: Mencegah reset saat load awal

// 1. Inisialisasi Data Awal
onMounted(async () => {
    const currentRoom = props.rooms.find(r => r.id === props.booking.room_id);
    if (currentRoom) {
        // Isi dropdown sesuai data lama
        selectedGedung.value = currentRoom.gedung;
        
        // Tunggu Vue update 'lantaiList' berdasarkan gedung baru
        await nextTick();
        
        selectedLantai.value = currentRoom.lantai;
        
        // Tunggu Vue update lagi, baru matikan flag inisialisasi
        await nextTick();
        isInitializing.value = false;
    } else {
        isInitializing.value = false;
    }
});

const gedungList = computed(() => {
    const list = props.rooms.map(r => r.gedung).filter(Boolean);
    return [...new Set(list)].sort();
});

const lantaiList = computed(() => {
    if (!selectedGedung.value) return [];
    const floors = props.rooms
        .filter(r => r.gedung === selectedGedung.value)
        .map(r => r.lantai)
        .filter(l => l !== null && l !== undefined);
    return [...new Set(floors)].sort((a, b) => a - b);
});

const ruanganList = computed(() => {
    if (!selectedGedung.value || selectedLantai.value === null) return [];
    return props.rooms.filter(r => 
        r.gedung === selectedGedung.value && 
        r.lantai == selectedLantai.value
    );
});

// WATCHER: Hanya reset jika user mengubah (bukan saat inisialisasi)
watch(selectedGedung, () => {
    if (!isInitializing.value) {
        selectedLantai.value = null;
        form.room_id = null;
    }
});

watch(selectedLantai, () => {
    if (!isInitializing.value) {
        form.room_id = null;
    }
});

const selectedRoomDetail = computed(() => {
    return props.rooms.find(r => r.id === form.room_id);
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        start_time: `${data.date} ${data.start_time}`,
        end_time: `${data.date} ${data.end_time}`,
    })).put(route('bookings.update', props.booking.id));
};
</script>

<template>
    <Head title="Edit Booking" />

    <AuthenticatedLayout>
        <div class="py-8 bg-gray-50 min-h-screen w-full">
            <!-- FULL WIDTH: Menggunakan w-full dan padding standar -->
            <div class="w-full px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Edit Pengajuan Booking</h2>
                        <p class="text-sm text-gray-600 mt-1">Perbarui detail peminjaman ruangan Anda.</p>
                    </div>
                    <Link 
                        :href="route('bookings.index')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-md text-gray-600 text-xs font-medium hover:bg-gray-50 hover:text-gray-900 transition shadow-sm w-fit"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Batal
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- FORMULIR -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
                            
                            <form @submit.prevent="submit" class="space-y-6">
                                
                                <!-- LOKASI -->
                                <div class="p-5 bg-gray-50 rounded-xl border border-gray-100 space-y-4">
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2 mb-2">
                                        1. Lokasi Ruangan
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                                            <select v-model="selectedGedung" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                                <option :value="null" disabled>-- Pilih Gedung --</option>
                                                <option v-for="g in gedungList" :key="g" :value="g">{{ g }}</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Lantai</label>
                                            <select v-model="selectedLantai" :disabled="!selectedGedung" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm disabled:bg-gray-100 disabled:text-gray-400">
                                                <option :value="null" disabled>-- Pilih Lantai --</option>
                                                <option v-for="l in lantaiList" :key="l" :value="l">Lantai {{ l }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruangan</label>
                                        <select v-model="form.room_id" :disabled="!selectedLantai" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm disabled:bg-gray-100 disabled:text-gray-400">
                                            <option :value="null" disabled>-- Pilih Ruangan --</option>
                                            <option v-for="room in ruanganList" :key="room.id" :value="room.id">
                                                {{ room.name }} (Kapasitas: {{ room.capacity }} orang)
                                            </option>
                                        </select>
                                        <div v-if="form.errors.room_id" class="text-red-500 text-xs mt-1">{{ form.errors.room_id }}</div>
                                    </div>
                                </div>

                                <!-- Info Ruangan -->
                                <div v-if="selectedRoomDetail" class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-start gap-4">
                                    <div class="bg-white p-2 rounded-lg shadow-sm text-indigo-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-indigo-900 text-sm">{{ selectedRoomDetail.name }}</h4>
                                        <p class="text-xs text-indigo-700 mt-1">
                                            Fasilitas: {{ selectedRoomDetail.facilities || 'Standar' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- DETAIL -->
                                <div class="space-y-4">
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2">
                                        2. Detail Kegiatan
                                    </h3>

                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan</label>
                                        <input 
                                            type="text" 
                                            id="title" 
                                            v-model="form.title"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                        />
                                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                                        <textarea 
                                            id="description" 
                                            v-model="form.description"
                                            rows="3"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- WAKTU -->
                                <div class="p-5 bg-gray-50 rounded-xl border border-gray-100 space-y-4">
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2 mb-2">
                                        3. Waktu Pelaksanaan
                                    </h3>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                        <input 
                                            type="date" 
                                            v-model="form.date"
                                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                        />
                                        <div v-if="form.errors.date" class="text-red-500 text-xs mt-1">{{ form.errors.date }}</div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                                            <input 
                                                type="time" 
                                                v-model="form.start_time"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                            />
                                            <div v-if="form.errors.start_time" class="text-red-500 text-xs mt-1">{{ form.errors.start_time }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                                            <input 
                                                type="time" 
                                                v-model="form.end_time"
                                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                            />
                                            <div v-if="form.errors.end_time" class="text-red-500 text-xs mt-1">{{ form.errors.end_time }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="pt-4 flex justify-end gap-3">
                                    <Link :href="route('bookings.index')" class="px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition">
                                        Batal
                                    </Link>
                                    <button 
                                        type="submit" 
                                        :disabled="form.processing"
                                        class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-xl hover:bg-indigo-700 transition shadow-lg transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    >
                                        <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>

                    <!-- KOLOM KANAN: Warning -->
                    <div class="hidden lg:block">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 sticky top-8 shadow-sm">
                            <h3 class="text-lg font-bold text-yellow-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Perhatian
                            </h3>
                            <p class="text-sm text-yellow-800 leading-relaxed mb-4">
                                Mengubah jadwal atau ruangan dapat menyebabkan booking Anda perlu ditinjau ulang oleh Admin.
                            </p>
                            <ul class="space-y-3 text-sm text-yellow-700">
                                <li class="flex gap-3">
                                    <span class="font-bold">•</span>
                                    <span>Pastikan tidak bentrok dengan jadwal lain.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>