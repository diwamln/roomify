<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { defineProps, ref, watch } from 'vue';
// Library Tambahan
import Swal from 'sweetalert2';
import { useAutoAnimate } from '@formkit/auto-animate/vue';

const props = defineProps({
  rooms: Object,   // SEKARANG OBJECT (Paginator), bukan Array
  filters: Object  // Data filter search
});

// 1. Setup Animasi Tabel
const [parent] = useAutoAnimate();
const form = useForm({});

// 2. Setup Pencarian Real-time
const search = ref(props.filters.search || '');
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('rooms.index'), { search: value }, {
            preserveState: true,
            replace: true
        });
    }, 300);
});

// 3. Fungsi Delete dengan SweetAlert
const deleteRoom = (id) => {
  Swal.fire({
        title: 'Hapus Ruangan?',
        text: "Data ruangan akan dihapus permanen! Booking terkait mungkin akan terpengaruh.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route('rooms.destroy', id), {
                onSuccess: () => {
                    Swal.fire('Terhapus!', 'Ruangan berhasil dihapus.', 'success')
                }
            });
        }
    })
};
</script>

<template>
  <Head title="Manajemen Ruangan" />

  <AuthenticatedLayout>
    <div class="py-6 w-full">
      <div class="w-full px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 min-h-[500px]">
          
          <!-- HEADER: Tombol Tambah & Search Bar -->
          <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            
            <!-- Tombol Tambah -->
            <Link
              :href="route('rooms.create')"
              class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition font-medium text-sm shadow-sm flex items-center gap-2 w-full sm:w-auto justify-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Tambah Ruangan</span>
            </Link>

            <!-- Search Input -->
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    v-model="search"
                    type="text" 
                    class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition" 
                    placeholder="Cari ruangan, gedung..."
                >
            </div>
          </div>
          
          <!-- Tabel Ruangan -->
          <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Nama Ruangan
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Lokasi
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Kapasitas
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                    Aksi
                  </th>
                </tr>
              </thead>
              <!-- Gunakan rooms.data karena Pagination -->
              <tbody class="bg-white divide-y divide-gray-200" ref="parent">
                <tr v-for="room in rooms.data" :key="room.id" class="hover:bg-gray-50 transition-colors">
                  
                  <!-- Nama -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">{{ room.name }}</div>
                  </td>
                  
                  <!-- Lokasi -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ room.gedung || '-' }}</div>
                    <div class="text-xs text-gray-500">Lantai {{ room.lantai || '-' }}</div>
                  </td>

                  <!-- Kapasitas -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                      {{ room.capacity }} orang
                    </span>
                  </td>

                  <!-- Aksi -->
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <Link :href="route('rooms.edit', room.id)" class="text-indigo-600 hover:text-indigo-900 font-semibold bg-indigo-50 px-2 py-1 rounded">
                      Edit
                    </Link>
                    <button @click="deleteRoom(room.id)" class="text-red-600 hover:text-red-900 font-semibold bg-red-50 px-2 py-1 rounded">
                      Hapus
                    </button>
                  </td>
                </tr>

                <!-- State Kosong -->
                <tr v-if="rooms.data.length === 0">
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                        Data ruangan tidak ditemukan.
                    </td>
                </tr>

              </tbody>
            </table>
          </div>

          <!-- FOOTER: PAGINATION -->
          <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4" v-if="rooms.links.length > 3">
              <div class="text-sm text-gray-600">
                  Menampilkan {{ rooms.from }} - {{ rooms.to }} dari {{ rooms.total }} data
              </div>
              <div class="flex gap-1">
                  <Component 
                    :is="link.url ? Link : 'span'"
                    v-for="(link, index) in rooms.links" 
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
  </AuthenticatedLayout>
</template>