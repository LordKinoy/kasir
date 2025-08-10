{{-- Header --}}
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Kasir') }}
    </h2>
</x-slot>

{{-- Main Content --}}
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-200">

            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="p-2 mb-2 bg-green-500 text-white rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-2 mb-2 bg-red-500 text-white rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form Tambah Barang --}}
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <select wire:model="barang_id" class="border p-2 rounded flex-1 bg-white dark:bg-gray-800 dark:text-gray-200">
                    <option value="">Pilih Barang</option>
                    @foreach($listBarang as $barang)
                        <option value="{{ $barang->id }}">
                            {{ $barang->nama }} (Stok: {{ $barang->stok }})
                        </option>
                    @endforeach
                </select>

                <input type="number" wire:model="jumlah" min="1" class="border p-2 rounded w-20 bg-white dark:bg-gray-800 dark:text-gray-200">
                <button wire:click="tambahBarang" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Tambah
                </button>
            </div>

            {{-- Tabel Keranjang --}}
            <table class="w-full border mb-4 text-gray-900 dark:text-gray-200">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-center">
                        <th class="border px-2 py-2">Barang</th>
                        <th class="border px-2 py-2">Harga</th>
                        <th class="border px-2 py-2">Jumlah</th>
                        <th class="border px-2 py-2">Subtotal</th>
                        <th class="border px-2 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keranjang as $index => $item)
                        <tr class="bg-white dark:bg-gray-800 text-center">
                            <td class="border px-2 py-2">{{ $item['nama'] }}</td>
                            <td class="border px-2 py-2">Rp {{ number_format($item['harga'],0,',','.') }}</td>
                            <td class="border px-2 py-2">{{ $item['jumlah'] }}</td>
                            <td class="border px-2 py-2">Rp {{ number_format($item['subtotal'],0,',','.') }}</td>
                            <td class="border px-2 py-2">
                                {{-- Tombol Hapus --}}
                                <button wire:click="hapusBarang({{ $index }})"
                                    class="text-red-500 hover:text-red-700" title="Hapus">
                                    <!-- Icon Tempat Sampah -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1h-6V4a1 1 0 011-1z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-gray-800 text-center">
                            <td colspan="5" class="border px-2 py-1 text-gray-500 dark:text-gray-400">
                                Keranjang kosong
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Total & Pembayaran --}}
            <div class="mb-4">
                <p>Total: <strong>Rp {{ number_format($totalHarga,0,',','.') }}</strong></p>
                <input type="number" wire:model="totalBayar" placeholder="Total Bayar" class="border p-2 rounded mt-2 w-40 bg-white dark:bg-gray-800 dark:text-gray-200">
                <p class="mt-2">Kembalian: <strong>Rp {{ number_format($kembalian,0,',','.') }}</strong></p>
            </div>

            {{-- Tombol Simpan --}}
            <button wire:click="simpanTransaksi" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Simpan Transaksi
            </button>

        </div>
    </div>
</div>
