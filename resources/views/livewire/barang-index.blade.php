{{-- Header --}}
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manajemen Barang') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-200">

            {{-- Alert sukses --}}
            @if($successMessage)
                <div class="p-2 mb-4 bg-green-500 text-white rounded">
                    {{ $successMessage }}
                </div>
            @endif

            {{-- Bar atas: Search di kiri, Tambah di kanan --}}
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari barang..." class="border p-2 rounded bg-white dark:bg-gray-800 dark:text-gray-200 flex-1">
                <button wire:click="openModalTambah" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Tambah Barang
                </button>
            </div>

            {{-- Tabel Barang --}}
            <table class="w-full border mb-4 text-gray-900 dark:text-gray-200">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-center">
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Harga</th>
                        <th class="border px-4 py-2">Stok</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listBarang as $barang)
                        <tr wire:key="barang-{{ $barang->id }}" class="bg-white dark:bg-gray-800 text-center">
                            <td class="border px-4 py-2">
                                {!! $search ? str_ireplace($search, '<mark class="bg-yellow-300">'.$search.'</mark>', e($barang->nama)) : e($barang->nama) !!}
                            </td>
                            <td class="border px-4 py-2">Rp {{ number_format($barang->harga,0,',','.') }}</td>
                            <td class="border px-4 py-2">{{ $barang->stok }}</td>
                            <td class="border px-4 py-2 space-x-2">
                                {{-- Tombol Edit --}}
                                <button wire:click="edit({{ $barang->id }})"
                                    class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5h2m-1-1v2m0 12h.01M21 21l-6-6M3 7l6 6m0 0l6-6m-6 6l6 6" />
                                    </svg>
                                </button>

                                {{-- Tombol Hapus --}}
                                @if($barang->transaksiDetails()->exists())
                                    <button disabled class="text-gray-400 cursor-not-allowed" title="Barang ini sedang digunakan di transaksi">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1h-6V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="delete({{ $barang->id }})"
                                        onclick="return confirm('Yakin ingin menghapus barang ini?')"
                                        class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1h-6V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="bg-white dark:bg-gray-800 text-center">
                            <td colspan="4" class="border px-4 py-2 text-gray-500 dark:text-gray-400">
                                Tidak ada data barang
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            {{ $listBarang->links() }}

            {{-- Modal Form --}}
            @if($showModal)
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-96 p-6">
                        <h2 class="text-lg font-bold mb-4">{{ $isEdit ? 'Edit Barang' : 'Tambah Barang' }}</h2>

                        <form wire:submit.prevent="save" class="space-y-3">
                            <input type="text" wire:model.defer="nama" placeholder="Nama Barang"
                                class="border p-2 rounded w-full bg-white dark:bg-gray-700 dark:text-gray-200">
                            <input type="number" wire:model.defer="harga" placeholder="Harga"
                                class="border p-2 rounded w-full bg-white dark:bg-gray-700 dark:text-gray-200">
                            <input type="number" wire:model.defer="stok" placeholder="Stok"
                                class="border p-2 rounded w-full bg-white dark:bg-gray-700 dark:text-gray-200">

                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" wire:click="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                                    Batal
                                </button>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
