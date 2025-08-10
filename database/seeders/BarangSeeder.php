<?php

// database/seeders/BarangSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    public function run()
    {
        Barang::create([
            'nama' => 'Pensil 2B',
            'stok' => 100,
            'harga' => 2500
        ]);

        Barang::create([
            'nama' => 'Buku Tulis',
            'stok' => 50,
            'harga' => 6000
        ]);

        Barang::create([
            'nama' => 'Penghapus',
            'stok' => 80,
            'harga' => 1500
        ]);
    }
}
