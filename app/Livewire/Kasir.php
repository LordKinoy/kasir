<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Livewire\Attributes\Layout;

class Kasir extends Component
{
    public $barang_id;
    public $jumlah = 1;
    public $keranjang = [];
    public $totalHarga = 0;
    public $totalBayar;
    public $kembalian = 0;

    public function updatedTotalBayar()
    {
        $this->hitungKembalian();
    }

    public function tambahBarang()
    {
        $barang = Barang::find($this->barang_id);
        if (!$barang) return;

        if ($barang->stok < $this->jumlah) {
            session()->flash('error', 'Stok barang tidak cukup');
            return;
        }

        // Cek apakah barang sudah ada di keranjang
        $index = collect($this->keranjang)->search(function ($item) use ($barang) {
            return $item['barang_id'] == $barang->id;
        });

        if ($index !== false) {
            // Barang sudah ada → update jumlah dan subtotal
            $jumlahBaru = $this->keranjang[$index]['jumlah'] + $this->jumlah;

            // Cek stok lagi untuk jumlah total
            if ($barang->stok < $jumlahBaru) {
                session()->flash('error', 'Stok barang tidak cukup untuk jumlah total');
                return;
            }

            $this->keranjang[$index]['jumlah'] = $jumlahBaru;
            $this->keranjang[$index]['subtotal'] = $this->keranjang[$index]['harga'] * $jumlahBaru;
        } else {
            // Barang belum ada → tambahkan baru
            $this->keranjang[] = [
                'barang_id' => $barang->id,
                'nama' => $barang->nama,
                'harga' => $barang->harga,
                'jumlah' => $this->jumlah,
                'subtotal' => $barang->harga * $this->jumlah
            ];
        }

        $this->hitungTotal();
        $this->reset(['barang_id', 'jumlah']);
    }

    public function hapusBarang($index)
    {
        unset($this->keranjang[$index]);
        $this->keranjang = array_values($this->keranjang);
        $this->hitungTotal();
    }

    public function hitungTotal()
    {
        $this->totalHarga = collect($this->keranjang)->sum('subtotal');
        $this->hitungKembalian();
    }

    public function hitungKembalian()
    {
        $this->kembalian = max(0, $this->totalBayar - $this->totalHarga);
    }

    public function simpanTransaksi()
    {
        if (empty($this->keranjang)) {
            session()->flash('error', 'Keranjang kosong');
            return;
        }

        if ($this->totalBayar < $this->totalHarga) {
            session()->flash('error', 'Uang bayar kurang');
            return;
        }

        $transaksi = Transaksi::create([
            'tanggal' => now(),
            'total_harga' => $this->totalHarga,
            'total_bayar' => $this->totalBayar,
            'kembalian' => $this->kembalian
        ]);

        foreach ($this->keranjang as $item) {
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'barang_id' => $item['barang_id'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga'],
                'subtotal' => $item['subtotal']
            ]);
        }

        $this->reset(['barang_id', 'jumlah', 'keranjang', 'totalHarga', 'totalBayar', 'kembalian']);
        session()->flash('success', 'Transaksi berhasil disimpan');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.kasir', [
            'listBarang' => Barang::all()
        ]);
    }
}
