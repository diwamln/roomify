<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
import { useAutoAnimate } from '@formkit/auto-animate/vue';
import Swal from 'sweetalert2';

const props = defineProps({
  bookings: Object, // Menerima Object Paginator (data, links, meta)
  filters: Object,  // Menyimpan state pencarian
});

// 1. Animasi Tabel
const [parent] = useAutoAnimate();

// 2. Logika Pencarian (Search)
const search = ref(props.filters.search || '');
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.bookings.index'), { search: value }, {
            preserveState: true,
            replace: true
        });
    }, 300);
});

// 3. Helper Format
const formatStatus = (status) => {
  if (status === 'pending') return { text: 'Menunggu', class: 'bg-yellow-100 text-yellow-800 border border-yellow-200' };
  if (status === 'approved') return { text: 'Disetujui', class: 'bg-green-100 text-green-800 border border-green-200' };
  if (status === 'rejected') return { text: 'Ditolak', class: 'bg-red-100 text-red-800 border border-red-200' };
  return { text: status, class: 'bg-gray-100 text-gray-800' };
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

// 4. Konfirmasi Aksi (Opsional, agar admin tidak salah klik)
const confirmAction = (action, id) => {
    const isApprove = action === 'approve';
    Swal.fire({
        title: isApprove ? 'Setujui Booking?' : 'Tolak Booking?',
        text: isApprove ? "Booking akan dijadwalkan." : "Booking akan dibatalkan.",
        icon: isApprove ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: isApprove ? '#10B981' : '#EF4444',
        confirmButtonText: isApprove ? 'Ya, Setujui' : 'Ya, Tolak'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route(`admin.bookings.${action}`, id), {}, {
                onSuccess: () => Swal.fire('Berhasil', `Booking telah ${isApprove ? 'disetujui' : 'ditolak'}.`, 'success')
            });
        }
    });
};
</script>

<template>
  <Head title="Manajemen Booking" />

  <AuthenticatedLayout>
    <div class="py-6 w-full">
      <div class="w-full px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 min-h-[500px]">
          
          <!-- HEADER: Judul & Search -->
          <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
              <h2 class="text-lg font-bold text-gray-800">Daftar Booking Masuk</h2>
              
              <!-- Input Search -->
              <div class="relative w-full sm:w-64">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                  </div>
                  <input 
                      v-model="search"
                      type="text" 
                      class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition" 
                      placeholder="Cari nama, ruangan..."
                  >
              </div>
          </div>
          
          <!-- TABEL BOOKING -->
          <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pemesan</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detail Kegiatan</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200" ref="parent">
                <tr v-for="booking in bookings.data" :key="booking.id" class="hover:bg-gray-50 transition">
                  
                  <!-- Kolom Pemesan -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">{{ booking.user.name }}</div>
                    <div class="text-xs text-gray-500">{{ booking.user.email }}</div>
                  </td>

                  <!-- Kolom Detail (Ruangan & Judul) -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ booking.room.name }}</div>
                    <div class="text-xs text-gray-500 truncate max-w-[200px]" :title="booking.title">
                        {{ booking.title }}
                    </div>
                  </td>

                  <!-- Kolom Waktu -->
                  <td class="px-6 py-4 whitespace-nowrap">
                     <div class="text-xs text-gray-500 font-medium">{{ formatDate(booking.start_time) }}</div>
                     <div class="text-xs text-gray-400">s/d {{ formatDate(booking.end_time).split(',')[1] || '-' }}</div>
                  </td>

                  <!-- Kolom Status -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="formatStatus(booking.status).class" class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ formatStatus(booking.status).text }}
                    </span>
                  </td>

                  <!-- Kolom Aksi -->
                  <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    
                    <!-- Tombol Muncul HANYA jika status 'Pending' -->
                    <div v-if="booking.status === 'pending'" class="flex items-center justify-center gap-2">
                      
                        <!-- Tombol Approve -->
                        <button 
                            @click="confirmAction('approve', booking.id)"
                            class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded hover:bg-green-200 transition"
                            title="Setujui"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Terima
                        </button>
                      
                        <!-- Tombol Reject -->
                        <button 
                            @click="confirmAction('reject', booking.id)"
                            class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded hover:bg-red-200 transition"
                            title="Tolak"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Tolak
                        </button>

                    </div>
                    
                    <!-- Jika sudah diproses -->
                    <span v-else class="text-gray-400 text-xs italic">
                        Selesai
                    </span>
                  </td>
                </tr>

                <!-- State Kosong -->
                <tr v-if="bookings.data.length === 0">
                  <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm">Belum ada data booking yang sesuai.</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- FOOTER: PAGINATION -->
          <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-2" v-if="bookings.links.length > 3">
              <div class="text-sm text-gray-600">
                  Menampilkan {{ bookings.from }} - {{ bookings.to }} dari {{ bookings.total }} data
              </div>
              <div class="flex gap-1">
                  <Component 
                    :is="link.url ? Link : 'span'"
                    v-for="(link, index) in bookings.links" 
                    :key="index"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1 text-xs border rounded transition font-medium"
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
  </AuthenticatedLayout>
</template>