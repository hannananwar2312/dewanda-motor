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
Route::post('/insight/ai', [KasirController::class, 'insightAi'])->name('insight.ai');

Route::get('/produk', [KasirController::class, 'produk'])->name('produk');
Route::post('/produk/simpan', [KasirController::class, 'simpanProduk'])->name('produk.simpan');
Route::post('/produk/hapus',  [KasirController::class, 'hapusProduk'])->name('produk.hapus');
Route::post('/produk/beli', [KasirController::class, 'simpanPembelian'])->name('produk.beli');
Route::post('/produk/kategori', [KasirController::class, 'simpanKategori'])->name('produk.kategori');
Route::post('/produk/supplier', [KasirController::class, 'simpanSupplier'])->name('produk.supplier');

Route::get('/pembukuan', [KasirController::class, 'pembukuan'])->name('pembukuan');
Route::post('/pembukuan/modal', [KasirController::class, 'simpanModal'])->name('pembukuan.modal');
Route::post('/pembukuan/modal/hapus', [KasirController::class, 'hapusModal'])->name('pembukuan.modal.hapus');