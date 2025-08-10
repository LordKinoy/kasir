<?php

use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Route;

Route::post('/transaksi/create', [KasirController::class, 'createTransaksi']);
Route::post('/transaksi/add-barang', [KasirController::class, 'addBarang']);
Route::post('/transaksi/complete', [KasirController::class, 'completeTransaksi']);
Route::delete('/transaksi/remove-barang', [KasirController::class, 'removeBarang']);
Route::delete('/transaksi/cancel', [KasirController::class, 'cancelTransaksi']);
