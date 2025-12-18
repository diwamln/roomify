<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Typed from 'typed.js';
import { ref, onMounted } from 'vue';


const typedTarget = ref(null);

onMounted(() => {
    new Typed(typedTarget.value, {
        strings: ['Siapakah yang akan menggunakan roomify hari ini?','Peminjaman kelas menjadi lebih mudah'],
        typeSpeed: 25,
        backSpeed: 15,
        loop: true,
    });
});


defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Selamat Datang di Roomify" />
    
    <div class="min-h-screen bg-white">
        <!-- Navbar (FULL WIDTH) -->
        <nav class="bg-black shadow-sm fixed w-full z-10 top-0 border-b border-gray-500 ">
            <!-- Ganti max-w-7xl menjadi w-full -->
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    
                    <!-- Kiri: Logo + Menu -->
                    <div class="flex items-center space-x-8">
                        <Link href="/" class="flex items-center">
                            <img
                                class="h-8 w-auto mr-2"
                                src="/logo-v1.png"
                                alt="Roomify Logo"
                            />
                            <span class="text-xl font-semibold text-gray-200">
                                Roomify
                            </span>
                        </Link>
                        
                        <!-- Menu Items -->
                        <div class="hidden md:flex items-center space-x-6">
                            <div class="relative group">
                                <button class="flex items-center text-sm font-medium text-gray-300 hover:text-indigo-600 transition">
                                    Fitur
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="relative group">
                                <button class="flex items-center text-sm font-medium text-gray-300 hover:text-indigo-600 transition">
                                    Ruangan
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <Link href="#" class="text-sm font-medium text-gray-300 hover:text-indigo-600 transition">
                                Cara Booking
                            </Link>
                            
                            <Link href="#" class="text-sm font-medium text-gray-300 hover:text-indigo-600 transition">
                                Bantuan
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Kanan: Search + Login/Register -->
                    <div class="flex items-center space-x-4">
                        <!-- Search Bar -->
                        <div class="hidden lg:block">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    placeholder="Search" 
                                    class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                />
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Tombol Log In -->
                        <Link
                            v-if="canLogin"
                            :href="route('login')"
                            class="text-sm font-medium text-gray-200 hover:text-indigo-600 transition duration-150 ease-in-out"
                        >
                            Log In
                        </Link>
                        
                        <!-- Tombol Sign Up -->
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-5 py-2 text-sm font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out"
                        >
                            Sign Up
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="pt-16">
            <section class="relative bg-gradient-to-br from-gray-900 via-indigo-950 to-gray-900 text-white overflow-hidden">
                <!-- Stars Background -->
                <div class="absolute inset-0">
                    <!-- Large stars -->
                    <div class="absolute top-20 left-10 w-1 h-1 bg-white rounded-full animate-pulse"></div>
                    <div class="absolute top-40 right-32 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                    <div class="absolute top-60 left-1/4 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 1s"></div>
                    <div class="absolute bottom-40 right-1/4 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 1.5s"></div>
                    <div class="absolute top-32 right-1/3 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 2s"></div>
                    <div class="absolute bottom-60 left-1/3 w-1.5 h-1.5 bg-white rounded-full animate-pulse" style="animation-delay: 0.8s"></div>
                    <div class="absolute top-1/2 right-20 w-1 h-1 bg-white rounded-full animate-pulse" style="animation-delay: 1.2s"></div>
                    
                    <!-- Small stars -->
                    <div class="absolute top-24 right-1/2 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute top-48 left-20 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute bottom-32 right-40 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute top-72 right-1/4 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute bottom-48 left-1/4 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute top-44 left-1/3 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute bottom-52 right-1/3 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute top-36 right-16 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute top-80 left-16 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                    <div class="absolute bottom-24 left-1/2 w-0.5 h-0.5 bg-white/60 rounded-full"></div>
                </div>
                
                <!-- Background decorative elements -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500 rounded-full filter blur-3xl"></div>
                    <div class="absolute bottom-20 right-10 w-96 h-96 bg-indigo-500 rounded-full filter blur-3xl"></div>
                </div>

                <!-- Container Full Width -->
                <div class="relative w-full px-4 sm:px-6 lg:px-8 py-12 md:py-20 pb-32">
                    <div class="text-center mb-12">
                        <!-- Main Heading -->
                        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-6">
                            Platform booking ruangan<br />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
                                untuk kampus modern
                            </span>
                        </h1>
                        
                        <!-- Sub-headline -->
                        <p class="text-lg md:text-xl text-indigo-200 max-w-2xl mx-auto mb-8">
                            <span ref="typedTarget" class="text-2xl"></span>
                            <!-- Siapakah yang akan menggunakan Roomify hari ini? -->
                        </p>
                        
                        <!-- Emoji -->
                        <div class="mb-12 flex justify-center">
                            <img src="/images/1.png" alt="Kampus" class="w-32 h-32" />
                        </div>
                    </div>

                    <!-- Category Cards Grid with Images -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-16 max-w-4xl mx-auto">
                        <!-- Mahasiswa -->
                        <div class="bg-indigo-800/40 backdrop-blur-sm border border-indigo-700/50 rounded-lg p-6 hover:bg-indigo-800/60 transition cursor-pointer">
                            <div class="mb-3 h-16 flex items-center justify-center">
                                <img src="/images/students.png" alt="Mahasiswa" class="w-16 h-16 object-contain" />
                            </div>
                            <p class="text-sm font-medium">Mahasiswa</p>
                            <p class="text-xs text-indigo-300 mt-1">Booking ruangan untuk kegiatan</p>
                        </div>
                        
                        <!-- Dosen -->
                        <div class="bg-indigo-800/40 backdrop-blur-sm border border-indigo-700/50 rounded-lg p-6 hover:bg-indigo-800/60 transition cursor-pointer">
                            <div class="mb-3 h-16 flex items-center justify-center">
                                <img src="/images/teacher.png" alt="Dosen" class="w-16 h-16 object-contain" />
                            </div>
                            <p class="text-sm font-medium">Dosen</p>
                            <p class="text-xs text-indigo-300 mt-1">Pesan lab & ruang kelas</p>
                        </div>
                        
                        <!-- Admin -->
                        <div class="bg-indigo-800/40 backdrop-blur-sm border border-indigo-700/50 rounded-lg p-6 hover:bg-indigo-800/60 transition cursor-pointer">
                            <div class="mb-3 h-16 flex items-center justify-center">
                                <img src="/images/admin.png" alt="Admin" class="w-16 h-16 object-contain" />
                            </div>
                            <p class="text-sm font-medium">Admin</p>
                            <p class="text-xs text-indigo-300 mt-1">Kelola & setujui booking</p>
                        </div>
                        
                        <!-- Staff -->
                        <div class="bg-indigo-800/40 backdrop-blur-sm border border-indigo-700/50 rounded-lg p-6 hover:bg-indigo-800/60 transition cursor-pointer">
                            <div class="mb-3 h-16 flex items-center justify-center">
                                <img src="/images/team.png" alt="Staff" class="w-16 h-16 object-contain" />
                            </div>
                            <p class="text-sm font-medium">Staff</p>
                            <p class="text-xs text-indigo-300 mt-1">Manajemen ruangan</p>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="text-center relative z-20">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-full text-indigo-950 bg-white hover:bg-gray-100 transition duration-200 shadow-2xl"
                        >
                            Mulai Booking Sekarang →
                        </Link>
                    </div>
                </div>

                <!-- Bottom decorative wave -->
                <div class="absolute bottom-0 left-0 right-0">
                    <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                    </svg>
                </div>
            </section>

            <!-- Featured Section -->
            <section class="py-20 bg-white">
                <!-- Container Full Width -->
                <div class="w-full px-4 sm:px-6 lg:px-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Card 1 -->
                        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-3xl p-8 shadow-lg hover:shadow-xl transition duration-300 flex flex-col">
                            <div class="mb-4">
                                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">FEATURED</span>
                            </div>
                            
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                Booking ruangan jadi mudah dengan sistem kalender real-time
                            </h2>
                            
                            <p class="text-gray-600 mb-6 leading-relaxed flex-grow">
                                Lihat ketersediaan semua ruangan dalam satu dashboard. Pesan ruang kelas, lab komputer, atau ruang rapat dengan sekali klik. Sistem otomatis mencegah double booking dan konflik jadwal.
                            </p>
                            
                            <div class="flex justify-center mb-6">
                                <div class="w-48 h-48 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-32 h-32 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <Link 
                                href="#"
                                class="inline-flex items-center px-6 py-3 border-2 border-gray-900 rounded-full font-medium text-gray-900 hover:bg-gray-900 hover:text-white transition duration-200 self-start"
                            >
                                Lihat Demo
                            </Link>
                        </div>

                        <!-- Card 2 -->
                        <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-3xl p-8 shadow-lg hover:shadow-xl transition duration-300 flex flex-col">
                            <div class="mb-4">
                                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">FEATURED</span>
                            </div>
                            
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                                Dashboard admin untuk kelola semua booking dengan efisien
                            </h2>
                            
                            <p class="text-gray-600 mb-6 leading-relaxed flex-grow">
                                Setujui atau tolak permintaan booking dengan cepat. Kelola data ruangan, kapasitas, dan fasilitas. Monitor penggunaan ruangan dengan laporan dan statistik lengkap. Semua dalam satu platform terpadu.
                            </p>
                            
                            <div class="flex justify-center mb-6">
                                <div class="w-48 h-48 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-32 h-32 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <Link 
                                href="#"
                                class="inline-flex items-center px-6 py-3 border-2 border-gray-900 rounded-full font-medium text-gray-900 hover:bg-gray-900 hover:text-white transition duration-200 self-start"
                            >
                                Pelajari Lebih Lanjut
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <!-- Social Media Section (FULL WIDTH) -->
            <div class="w-full px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <!-- Instagram -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-500 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">Instagram</h3>
                        <p class="text-sm text-gray-600">Follow @RoomifyCampus di Instagram</p>
                    </div>

                    <!-- Twitter/X -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">X</h3>
                        <p class="text-sm text-gray-600">Follow @RoomifyApp on X</p>
                    </div>

                    <!-- LinkedIn -->
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">LinkedIn</h3>
                        <p class="text-sm text-gray-600">Connect dengan Roomify Community di LinkedIn</p>
                    </div>
                </div>
            </div>

            <!-- Footer Links (FULL WIDTH) -->
            <div class="border-t border-gray-200">
                <div class="w-full px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                        <!-- Tentang Roomify -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Tentang Roomify</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Tentang Kami</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Kampus Partner</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Karir</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Blog</a></li>
                            </ul>
                        </div>

                        <!-- Fitur -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Fitur</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Booking Ruangan</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Kalender</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Dashboard Admin</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Laporan & Statistik</a></li>
                            </ul>
                        </div>

                        <!-- Jenis Ruangan -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Jenis Ruangan</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Ruang Kelas</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Lab Komputer</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Ruang Rapat</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Auditorium</a></li>
                            </ul>
                        </div>

                        <!-- Dokumentasi -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Dokumentasi</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Panduan Mahasiswa</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Panduan Dosen</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Panduan Admin</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">FAQ</a></li>
                            </ul>
                        </div>

                        <!-- Bantuan -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Bantuan</h4>
                            <ul class="space-y-2">
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Hubungi Kami</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Support Center</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Lapor Bug</a></li>
                                <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Request Fitur</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer (FULL WIDTH) -->
            <div class="border-t border-gray-200 bg-gray-50">
                <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <!-- Left: Logo & Copyright -->
                        <div class="flex items-center mb-4 md:mb-0">
                            <img src="/logo.png" alt="Roomify" class="h-6 w-auto mr-2" />
                            <span class="text-sm text-gray-600">© 2024 Roomify. All rights reserved.</span>
                        </div>

                        <!-- Center: Links -->
                        <div class="flex items-center space-x-6 mb-4 md:mb-0">
                            <a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Privacy</a>
                            <a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Terms</a>
                            <a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Kebijakan Kampus</a>
                        </div>

                        <!-- Right: Newsletter & Language -->
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">Dapatkan update via email</span>
                            <button class="px-6 py-2 bg-black text-white text-sm font-medium rounded-full hover:bg-gray-800 transition">
                                Subscribe
                            </button>
                            <button class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                                🌐 Bahasa Indonesia
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>