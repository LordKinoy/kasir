<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Transaksi;

class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';
    protected $fillable = ['transaksi_id', 'barang_id', 'jumlah', 'harga_satuan', 'subtotal'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($detail) {
            $barang = Barang::find($detail->barang_id);
            if ($barang) {
                $barang->stok -= $detail->jumlah;
                $barang->save();
            }

            $total = self::where('transaksi_id', $detail->transaksi_id)->sum('subtotal');
            Transaksi::where('id', $detail->transaksi_id)->update(['total_harga' => $total]);
        });

        static::deleted(function ($detail) {
            $barang = Barang::find($detail->barang_id);
            if ($barang) {
                $barang->stok += $detail->jumlah;
                $barang->save();
            }

            $total = self::where('transaksi_id', $detail->transaksi_id)->sum('subtotal');
            Transaksi::where('id', $detail->transaksi_id)->update(['total_harga' => $total]);
        });
    }
}
