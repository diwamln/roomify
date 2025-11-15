<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Booking Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bookings.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="room_id" :value="__('Pilih Ruangan')" />
                            <select id="room_id" name="room_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled {{ old('room_id') ? '' : 'selected' }}>-- Pilih Ruangan --</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} (Kapasitas: {{ $room->capacity }} orang)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('room_id')" />
                        </div>

                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal Booking')" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                        </div>

                        <div>
                            <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                            <x-text-input id="jam_mulai" name="jam_mulai" type="time" class="mt-1 block w-full" :value="old('jam_mulai')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jam_mulai')" />
                        </div>

                        <div>
                            <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                            <x-text-input id="jam_selesai" name="jam_selesai" type="time" class="mt-1 block w-full" :value="old('jam_selesai')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('jam_selesai')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Booking Sekarang') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>