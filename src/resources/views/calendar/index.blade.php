<x-app-layout>
    {{-- TAMBAHKAN BARIS INI DI ATAS --}}
    @push('scripts')
        @vite(['resources/js/calendar.js'])
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kalender Ketersediaan Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div id="calendar"></div>

                </div>
            </div>
        </div>
    </div>

    {{-- 
        Kita HAPUS semua <script> ... </script> dari sini.
        Semua logika JavaScript akan dipindahkan ke calendar.js
    --}}

</x-app-layout>