<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Kasir;
use App\Livewire\BarangIndex;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\TransaksiDetail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $today = now()->toDateString();

    // Total transaksi hari ini
    $totalHariIni = Transaksi::whereDate('tanggal', $today)->sum('total_harga');

    // Jumlah transaksi hari ini
    $jumlahTransaksi = Transaksi::whereDate('tanggal', $today)->count();

    // Barang stok kurang dari 10
    $stokKurang = Barang::where('stok', '<', 10)->count();

    // 3 Barang terlaris (berdasarkan total jumlah terjual)
    $barangTerlaris = TransaksiDetail::select('barang_id')
        ->selectRaw('SUM(jumlah) as total_terjual')
        ->groupBy('barang_id')
        ->orderByDesc('total_terjual')
        ->with('barang') // pastikan relasi sudah ada di model
        ->limit(3)
        ->get();

    return view('dashboard', compact(
        'totalHariIni',
        'jumlahTransaksi',
        'stokKurang',
        'barangTerlaris'
    ));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/kasir', Kasir::class)->name('kasir.index');
    Route::get('/barang', BarangIndex::class)->name('barang.index');
});

require __DIR__ . '/auth.php';
