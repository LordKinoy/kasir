<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $fillable = ['nama', 'stok', 'harga'];

    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class, 'barang_id');
    }
}
