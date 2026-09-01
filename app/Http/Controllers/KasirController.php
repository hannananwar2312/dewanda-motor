<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasirController extends Controller
{
    private function products(): array
    {
        return [
            ['id' => 1,  'name' => 'Yamahalube SuperSport 1L', 'sku' => '#120/YD990',  'price' => 50000,   'stock' => 12, 'sold' => 120, 'cat' => 'oli',     'emoji' => '🛢️', 'tint' => 'bg-blue-50'],
            ['id' => 2,  'name' => 'ShockBreaker Yamaha',       'sku' => '#120/YD790',  'price' => 150000,  'stock' => 5,  'sold' => 18,  'cat' => 'lainnya', 'emoji' => '🔧', 'tint' => 'bg-rose-50'],
            ['id' => 3,  'name' => 'Kampas Rem Honda PCX',      'sku' => '#120/YD980',  'price' => 25000,   'stock' => 12, 'sold' => 95,  'cat' => 'rem',     'emoji' => '🛑', 'tint' => 'bg-slate-100'],
            ['id' => 4,  'name' => 'Discbrake 777 220mm',       'sku' => '#120/JN2340', 'price' => 350000,  'stock' => 16, 'sold' => 22,  'cat' => 'rem',     'emoji' => '💿', 'tint' => 'bg-amber-50'],
            ['id' => 5,  'name' => 'Headlamp Yamaha Nmax 155',  'sku' => '#120/BG750',  'price' => 2500000, 'stock' => 5,  'sold' => 8,   'cat' => 'lainnya', 'emoji' => '💡', 'tint' => 'bg-yellow-50'],
            ['id' => 6,  'name' => 'Baut Set Warna Titanium',   'sku' => '#120/BT990',  'price' => 25000,   'stock' => 50, 'sold' => 60,  'cat' => 'lainnya', 'emoji' => '🔩', 'tint' => 'bg-fuchsia-50'],
            ['id' => 7,  'name' => 'Ban Tubeless IRC 90/80',    'sku' => '#120/BN110',  'price' => 275000,  'stock' => 8,  'sold' => 40,  'cat' => 'ban',     'emoji' => '🛞', 'tint' => 'bg-zinc-100'],
            ['id' => 8,  'name' => 'Velg Racing Nmax',          'sku' => '#120/VG220',  'price' => 1200000, 'stock' => 3,  'sold' => 5,   'cat' => 'ban',     'emoji' => '⭕', 'tint' => 'bg-neutral-100'],
            ['id' => 9,  'name' => 'Busi NGK Iridium',          'sku' => '#120/BS330',  'price' => 85000,   'stock' => 30, 'sold' => 150, 'cat' => 'busi',    'emoji' => '⚡', 'tint' => 'bg-orange-50'],
            ['id' => 10, 'name' => 'Busi Denso Standard',       'sku' => '#120/BS340',  'price' => 45000,   'stock' => 22, 'sold' => 88,  'cat' => 'busi',    'emoji' => '⚡', 'tint' => 'bg-red-50'],
            ['id' => 11, 'name' => 'Filter Oli Yamaha',         'sku' => '#120/FL440',  'price' => 35000,   'stock' => 24, 'sold' => 70,  'cat' => 'filter',  'emoji' => '🌀', 'tint' => 'bg-emerald-50'],
            ['id' => 12, 'name' => 'Oli Shell Advance AX7',     'sku' => '#120/OL550',  'price' => 65000,   'stock' => 18, 'sold' => 110, 'cat' => 'oli',     'emoji' => '🛢️', 'tint' => 'bg-sky-50'],
        ];
    }

    public function showLogin()
    {
        if (session('kasir')) {
            return redirect('/kasir');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($data['username'] === 'kasir' && $data['password'] === 'kasir123') {
            session(['kasir' => $data['username']]);

            return redirect('/kasir');
        }

        return back()
            ->withErrors(['login' => 'Username atau password salah.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget('kasir');

        return redirect('/login');
    }

    public function index()
    {
        if (!session('kasir')) {
            return redirect('/login');
        }

        return view('kasir', [
            'products' => $this->products(),
            'user'     => session('kasir'),
            'invoice'  => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) rand(100, 999), 3, '0', STR_PAD_LEFT),
        ]);
    }

    public function insight()
    {
        if (!session('kasir')) {
            return redirect('/login');
        }

        $products = $this->products();

        $terlaris = collect($products)->sortByDesc('sold')->take(5)->values()->all();
        $menipis  = collect($products)->filter(fn ($p) => $p['stock'] <= 8)->sortBy('stock')->values()->all();

        $totalProduk  = count($products);
        $totalTerjual = array_sum(array_column($products, 'sold'));
        $totalOmzet   = array_sum(array_map(fn ($p) => $p['price'] * $p['sold'], $products));
        $nilaiStok    = array_sum(array_map(fn ($p) => $p['price'] * $p['stock'], $products));

        $top    = $terlaris[0] ?? null;
        $kritis = $menipis[0] ?? null;

        $ringkasan = 'Produk terlaris saat ini adalah ' . ($top['name'] ?? '-') . ' dengan ' . ($top['sold'] ?? 0) . ' unit terjual. ';
        if (count($menipis)) {
            $ringkasan .= 'Ada ' . count($menipis) . ' produk yang stoknya menipis dan perlu segera di-restock, terutama ' . $kritis['name'] . ' (sisa ' . $kritis['stock'] . ' unit).';
        } else {
            $ringkasan .= 'Semua stok produk masih dalam kondisi aman.';
        }

        return view('insight', compact(
            'terlaris', 'menipis', 'totalProduk', 'totalTerjual', 'totalOmzet', 'nilaiStok', 'ringkasan'
        ));
    }

    public function pembukuan()
    {
        if (!session('kasir')) {
            return redirect('/login');
        }

        $products = $this->products();
        $methods  = ['Cash', 'QRIS'];

        mt_srand(2026); // biar data dummy konsisten tiap dibuka

        // ===== Riwayat Penjualan =====
        $rows = [];
        for ($i = 0; $i < 150; $i++) {
            $p    = $products[mt_rand(0, count($products) - 1)];
            $qty  = mt_rand(1, 4);
            $date = now()->subDays(mt_rand(0, 89));

            $rows[] = [
                'tanggal'    => $date->format('Y-m-d'),
                'invoice'    => 'INV-' . $date->format('Ymd') . '-' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT),
                'produk'     => $p['name'],
                'jumlah'     => $qty,
                'harga'      => $p['price'],
                'total'      => $p['price'] * $qty,
                'pembayaran' => $methods[mt_rand(0, 1)],
            ];
        }
        usort($rows, fn ($a, $b) => strcmp($b['tanggal'], $a['tanggal'])); // terbaru dulu

        // ===== Riwayat Pembelian (beli stok dari supplier) =====
        $suppliers = ['CV Maju Motor', 'PT Sinar Sparepart', 'Toko Jaya Ban', 'UD Berkah Oli'];
        mt_srand(777);
        $pembelian = [];
        for ($i = 0; $i < 80; $i++) {
            $p         = $products[mt_rand(0, count($products) - 1)];
            $qty       = mt_rand(5, 30);
            $date      = now()->subDays(mt_rand(0, 89));
            $hargaBeli = (int) round($p['price'] * 0.7); // harga beli ~70% harga jual

            $pembelian[] = [
                'tanggal'  => $date->format('Y-m-d'),
                'faktur'   => 'PO-' . $date->format('Ymd') . '-' . str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT),
                'produk'   => $p['name'],
                'jumlah'   => $qty,
                'harga'    => $hargaBeli,
                'total'    => $hargaBeli * $qty,
                'supplier' => $suppliers[mt_rand(0, count($suppliers) - 1)],
            ];
        }
        usort($pembelian, fn ($a, $b) => strcmp($b['tanggal'], $a['tanggal']));

        return view('pembukuan', [
            'user'      => session('kasir'),
            'rows'      => $rows,
            'pembelian' => $pembelian,
        ]);
    }

    public function produk()
    {
        if (!session('kasir')) {
            return redirect('/login');
        }

        $products = $this->products();

        $statusOf = function ($stock) {
            if ($stock <= 0) return 'habis';
            if ($stock <= 8) return 'menipis';
            return 'tersedia';
        };

        $rows = array_map(function ($p) use ($statusOf) {
            $p['status'] = $statusOf($p['stock']);
            return $p;
        }, $products);

        $totalProduk  = count($rows);
        $stokTersedia = count(array_filter($rows, fn ($p) => $p['status'] === 'tersedia'));
        $stokMenipis  = count(array_filter($rows, fn ($p) => $p['status'] === 'menipis'));
        $stokHabis    = count(array_filter($rows, fn ($p) => $p['status'] === 'habis'));
        $nilaiStok    = array_sum(array_map(fn ($p) => $p['price'] * $p['stock'], $rows));

        return view('produk', [
            'user'         => session('kasir'),
            'rows'         => array_values($rows),
            'totalProduk'  => $totalProduk,
            'stokTersedia' => $stokTersedia,
            'stokMenipis'  => $stokMenipis,
            'stokHabis'    => $stokHabis,
            'nilaiStok'    => $nilaiStok,
        ]);
    }
}