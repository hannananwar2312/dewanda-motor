<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirController;

Route::get('/', fn () => redirect('/kasir'));

Route::get('/login', [KasirController::class, 'showLogin'])->name('login');
Route::post('/login', [KasirController::class, 'login']);
Route::post('/logout', [KasirController::class, 'logout'])->name('logout');

Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
Route::post('/kasir/simpan', [KasirController::class, 'simpanTransaksi'])->name('kasir.simpan');
Route::get('/insight', [KasirController::class, 'insight'])->name('insight');
Route::get('/pembukuan', [KasirController::class, 'pembukuan'])->name('pembukuan');
Route::get('/produk', [KasirController::class, 'produk'])->name('produk');
Route::post('/produk/simpan', [KasirController::class, 'simpanProduk'])->name('produk.simpan');
Route::post('/produk/hapus',  [KasirController::class, 'hapusProduk'])->name('produk.hapus');
Route::post('/produk/beli', [KasirController::class, 'simpanPembelian'])->name('produk.beli');