<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    room: Object
});

const form = useForm({
    name: props.room.name,
    capacity: props.room.capacity,
    gedung: props.room.gedung,
    lantai: props.room.lantai,
    facilities: props.room.facilities,
    description: props.room.description,
});

const submit = () => {
    form.put(route('rooms.update', props.room.id));
};
</script>

<template>
    <Head title="Edit Ruangan" />

    <AuthenticatedLayout>
        <div class="py-8 bg-gray-50 min-h-screen w-full">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Edit Ruangan: {{ room.name }}</h2>
                        <p class="text-sm text-gray-600 mt-1">Perbarui informasi ruangan ini.</p>
                    </div>
                    <Link 
                        :href="route('rooms.index')"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-300 rounded-md text-gray-600 text-xs font-medium hover:bg-gray-50 hover:text-gray-900 transition shadow-sm w-fit"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </Link>
                </div>

                <!-- FORM FULL WIDTH (Hapus max-w-4xl) -->
                <form @submit.prevent="submit" class="w-full">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        <!-- GROUP 1: INFO UTAMA -->
                        <div class="p-6 md:p-8 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">1</div>
                                Informasi Utama
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama Ruangan -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Nama Ruangan</label>
                                    <input 
                                        type="text" 
                                        v-model="form.name"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                                </div>

                                <!-- Kapasitas -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Kapasitas (Orang)</label>
                                    <input 
                                        type="number" 
                                        v-model="form.capacity"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                        required
                                    />
                                    <div v-if="form.errors.capacity" class="text-red-500 text-xs mt-1">{{ form.errors.capacity }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- GROUP 2: LOKASI -->
                        <div class="p-6 md:p-8 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">2</div>
                                Lokasi
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Gedung -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Nama Gedung</label>
                                    <input 
                                        type="text" 
                                        v-model="form.gedung"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                    />
                                    <div v-if="form.errors.gedung" class="text-red-500 text-xs mt-1">{{ form.errors.gedung }}</div>
                                </div>

                                <!-- Lantai -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Lantai Ke-</label>
                                    <input 
                                        type="number" 
                                        v-model="form.lantai"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                    />
                                    <div v-if="form.errors.lantai" class="text-red-500 text-xs mt-1">{{ form.errors.lantai }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- GROUP 3: DETAIL -->
                        <div class="p-6 md:p-8">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">3</div>
                                Detail & Fasilitas
                            </h3>

                            <div class="space-y-4">
                                <!-- Fasilitas -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Fasilitas</label>
                                    <input 
                                        type="text" 
                                        v-model="form.facilities"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                    />
                                    <div v-if="form.errors.facilities" class="text-red-500 text-xs mt-1">{{ form.errors.facilities }}</div>
                                </div>

                                <!-- Deskripsi -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase">Deskripsi Tambahan</label>
                                    <textarea 
                                        v-model="form.description"
                                        rows="3"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm"
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER ACTIONS -->
                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                            <Link 
                                :href="route('rooms.index')"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md flex items-center gap-2 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Perbarui Ruangan</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>