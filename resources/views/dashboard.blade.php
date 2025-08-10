<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistik Hari Ini --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Penjualan Hari Ini</h3>
                    <p class="text-2xl font-bold text-green-500 mt-2">
                        Rp {{ number_format($totalHariIni, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Jumlah Transaksi</h3>
                    <p class="text-2xl font-bold text-blue-500 mt-2">{{ $jumlahTransaksi }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Barang Stok < 10</h3>
                    <p class="text-2xl font-bold text-red-500 mt-2">{{ $stokKurang }}</p>
                </div>
            </div>

            {{-- Tabel Barang Terlaris --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">3 Barang Terlaris</h3>
                <table class="w-full text-gray-700 dark:text-gray-300">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left">Barang</th>
                            <th class="px-4 py-2 text-center">Total Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangTerlaris as $item)
                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                <td class="px-4 py-2">{{ $item->barang->nama ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->total_terjual }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-2 text-gray-500 dark:text-gray-400">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
