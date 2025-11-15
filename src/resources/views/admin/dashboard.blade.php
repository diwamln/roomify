<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard (Permintaan Booking)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemesan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ruangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pendingBookings as $booking)
                                <tr>
                                    <td class="px-6 py-4">{{ $booking->user->name }}</td>
                                    <td class="px-6 py-4">{{ $booking->room->name }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($booking->tanggal)->format('d M Y') }}</td>
                                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <x-primary-button class="bg-green-600 hover:bg-green-700">
                                                Setuju
                                            </x-primary-button>
                                        </form>

                                        <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST" class="inline-block ml-2">
                                            @csrf
                                            @method('PATCH')
                                            <x-danger-button>
                                                Tolak
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada permintaan booking yang pending.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pendingBookings->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>