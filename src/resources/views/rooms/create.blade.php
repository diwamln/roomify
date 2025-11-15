<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Ruangan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('rooms.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nama Ruangan')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="capacity" :value="__('Kapasitas (Orang)')" />
                            <x-text-input id="capacity" name="capacity" type="number" class="mt-1 block w-full" :value="old('capacity')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                        </div>

                        <div>
                            <x-input-label for="facilities" :value="__('Fasilitas (pisahkan dengan koma)')" />
                            <textarea id="facilities" name="facilities" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('facilities') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('facilities')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>