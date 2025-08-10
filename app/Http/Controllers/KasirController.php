<?php

// app/Http/Controllers/KasirController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;

class KasirController extends Controller
{
    // Buat transaksi baru (awal)
    public function createTransaksi()
    {
        $transaksi = Transaksi::create([
            'tanggal' => now(),
            'total_harga' => 0,
            'total_bayar' => 0,
            'kembalian' => 0
        ]);

        return response()->json([
            'message' => 'Transaksi baru dibuat',
            'transaksi_id' => $transaksi->id
        ]);
    }

    // Tambah barang ke transaksi
    public function addBarang(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($barang->stok < $request->jumlah) {
            return response()->json(['error' => 'Stok barang tidak mencukupi'], 400);
        }

        TransaksiDetail::create([
            'transaksi_id' => $request->transaksi_id,
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $barang->harga,
            'subtotal' => $barang->harga * $request->jumlah
        ]);

        return response()->json(['message' => 'Barang berhasil ditambahkan ke transaksi']);
    }

    // Selesaikan transaksi (hitung kembalian)
    public function completeTransaksi(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'total_bayar' => 'required|numeric|min:0'
        ]);

        $transaksi = Transaksi::findOrFail($request->transaksi_id);

        if ($request->total_bayar < $transaksi->total_harga) {
            return response()->json(['error' => 'Uang yang dibayar kurang'], 400);
        }

        $transaksi->tanggal = now();
        $transaksi->total_bayar = $request->total_bayar;
        $transaksi->kembalian = $request->total_bayar - $transaksi->total_harga;
        $transaksi->save();

        return response()->json([
            'message' => 'Transaksi selesai',
            'tanggal' => $transaksi->tanggal,
            'total_harga' => $transaksi->total_harga,
            'total_bayar' => $transaksi->total_bayar,
            'kembalian' => $transaksi->kembalian
        ]);
    }

    // Hapus 1 barang dari transaksi
    public function removeBarang(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:transaksi_detail,id'
        ]);

        $detail = \App\Models\TransaksiDetail::findOrFail($request->detail_id);
        $detail->delete(); // stok otomatis rollback lewat event model

        return response()->json(['message' => 'Barang dihapus dan stok dikembalikan']);
    }

    // Batalkan transaksi (hapus semua detail & rollback stok)
    public function cancelTransaksi(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id'
        ]);

        $transaksi = \App\Models\Transaksi::findOrFail($request->transaksi_id);

        // Hapus semua detail → stok rollback otomatis
        foreach ($transaksi->details as $detail) {
            $detail->delete();
        }

        // Hapus transaksi utama
        $transaksi->delete();

        return response()->json(['message' => 'Transaksi dibatalkan dan stok dikembalikan']);
    }
}
