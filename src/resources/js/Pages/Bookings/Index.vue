<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
// Import Library Tambahan
import Swal from 'sweetalert2';
import { useAutoAnimate } from '@formkit/auto-animate/vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue';

// Props dari Controller
const props = defineProps({
  bookings: Object, // SEKARANG OBJECT (karena Paginator), bukan Array
  filters: Object   // Data pencarian
});

// Setup AutoAnimate (Animasi Tabel)
const [parent] = useAutoAnimate();

// State Search
const search = ref(props.filters.search || '');
let searchTimeout = null;

// Watcher untuk Pencarian Otomatis
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('bookings.index'), { search: value }, {
            preserveState: true, // Jangan refresh full
            replace: true        // Jangan tumpuk history
        });
    }, 300); // Tunggu 300ms setelah user berhenti mengetik
});

// State Modal Detail
const showModal = ref(false);
const selectedBooking = ref(null);

const openDetailModal = (booking) => {
    selectedBooking.value = booking;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => selectedBooking.value = null, 200);
};

// Fungsi Delete dengan SweetAlert
const deleteBooking = (id) => {
    Swal.fire({
        title: 'Batalkan Booking?',
        text: "Apakah Anda yakin ingin membatalkan booking ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1F2937',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('bookings.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Dibatalkan!', 'Booking berhasil dibatalkan.', 'success');
                }
            });
        }
    })
};

// Helper Format
const formatStatus = (status) => {
  if (status === 'pending') return { text: 'Menunggu', class: 'bg-yellow-100 text-yellow-800 border border-yellow-200' };
  if (status === 'approved') return { text: 'Disetujui', class: 'bg-green-100 text-green-800 border border-green-200' };
  if (status === 'rejected') return { text: 'Ditolak', class: 'bg-red-100 text-red-800 border border-red-200' };
  return { text: status, class: 'bg-gray-100 text-gray-800' };
};

const formatDate = (dateString) => {
  if(!dateString) return '-';
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
  <Head title="Booking Saya" />

  <AuthenticatedLayout>
    <div class="py-6 w-full">
      <div class="w-full px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 relative min-h-[500px] border border-gray-200">
          
          <!-- HEADER & SEARCH BAR -->
          <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <!-- Tombol Buat Baru -->
            <Link 
              :href="route('bookings.create')" 
              class="inline-flex items-center gap-2 bg-gray-900 border border-transparent text-white hover:bg-gray-800 font-medium py-2 px-4 rounded-lg transition-all duration-200 shadow-sm text-sm w-full sm:w-auto justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Buat Booking Baru
            </Link>

            <!-- Input Pencarian -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    v-model="search"
                    type="text" 
                    class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition" 
                    placeholder="Cari kegiatan atau ruangan..."
                >
            </div>
          </div>

          <!-- Tabel -->
          <div class="overflow-x-auto rounded-lg border border-gray-200">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ruangan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kegiatan</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th> 
                  </tr>
                </thead>
                <!-- Gunakan bookings.data karena pagination -->
                <tbody class="bg-white divide-y divide-gray-200" ref="parent">
                  <tr v-for="booking in bookings.data" :key="booking.id" class="hover:bg-gray-50 transition">
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-bold text-gray-900">{{ booking.room.name }}</div>
                      <div class="text-xs text-gray-500 mt-0.5">
                        <span v-if="booking.room.gedung">{{ booking.room.gedung }}</span>
                        <span v-if="booking.room.lantai"> • Lt. {{ booking.room.lantai }}</span>
                      </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ booking.title }}</div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-500">
                            {{ formatDate(booking.start_time) }} <br>
                            <span class="text-gray-400">s/d {{ formatDate(booking.end_time).split(' ')[3] }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="formatStatus(booking.status).class" class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ formatStatus(booking.status).text }}
                      </span>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center gap-2">
                            <button @click="openDetailModal(booking)" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-1.5 rounded-md hover:bg-blue-100 transition" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                            <Link v-if="booking.status === 'pending'" :href="route('bookings.edit', booking.id)" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 p-1.5 rounded-md hover:bg-yellow-100 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </Link>
                            <button v-if="booking.status === 'pending'" @click="deleteBooking(booking.id)" class="text-red-600 hover:text-red-900 bg-red-50 p-1.5 rounded-md hover:bg-red-100 transition" title="Batalkan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                  </tr>
                   <tr v-if="bookings.data.length === 0">
                    <td colspan="5" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center justify-center text-gray-500">
                          <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                          <p class="text-base">Data tidak ditemukan.</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
          </div>

          <!-- FOOTER: PAGINATION -->
          <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4" v-if="bookings.links.length > 3">
              <div class="text-sm text-gray-500">
                  Menampilkan {{ bookings.from }} - {{ bookings.to }} dari {{ bookings.total }} data
              </div>
              <div class="flex gap-1">
                  <Component 
                    :is="link.url ? Link : 'span'"
                    v-for="(link, index) in bookings.links" 
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1 text-xs border rounded-md transition font-medium"
                    :class="{
                        'bg-gray-900 text-white border-gray-900': link.active,
                        'bg-white text-gray-700 hover:bg-gray-50': !link.active && link.url,
                        'text-gray-400 border-gray-200 cursor-not-allowed': !link.url
                    }"
                  />
              </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal Detail (Headless UI) -->
    <TransitionRoot appear :show="showModal" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4 text-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
              <DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <DialogTitle as="h3" class="text-lg font-bold text-gray-900">Detail Booking</DialogTitle>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4" v-if="selectedBooking">
                    <div :class="formatStatus(selectedBooking.status).class" class="p-3 rounded-lg text-center font-semibold text-sm border">
                        Status: {{ formatStatus(selectedBooking.status).text }}
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ruangan</label>
                            <p class="text-gray-900 font-medium">{{ selectedBooking.room.name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Lokasi</label>
                            <p class="text-gray-900 font-medium">{{ selectedBooking.room.gedung || '-' }}, Lt. {{ selectedBooking.room.lantai || '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Kegiatan</label>
                        <p class="text-gray-900 font-medium text-lg">{{ selectedBooking.title }}</p>
                        <p v-if="selectedBooking.description" class="text-gray-600 text-sm mt-1">{{ selectedBooking.description }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Mulai</span>
                            <span class="text-sm font-bold text-gray-800">{{ formatDate(selectedBooking.start_time) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Selesai</span>
                            <span class="text-sm font-bold text-gray-800">{{ formatDate(selectedBooking.end_time) }}</span>
                        </div>
                    </div>
                    <div v-if="selectedBooking.admin_notes" class="bg-red-50 p-4 rounded-xl border border-red-100">
                         <label class="block text-xs font-bold text-red-600 uppercase mb-1">Catatan Admin:</label>
                         <p class="text-red-800 text-sm">{{ selectedBooking.admin_notes }}</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button @click="closeModal" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-900 hover:bg-indigo-200 focus:outline-none transition">Tutup</button>
                </div>

              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

  </AuthenticatedLayout>
</template>