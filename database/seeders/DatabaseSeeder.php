<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Akun login =====
        User::create(['name' => 'Administrator', 'username' => 'admin', 'role' => 'admin', 'password' => 'admin123']);
        User::create(['name' => 'Kasir',         'username' => 'kasir', 'role' => 'kasir', 'password' => 'kasir123']);

        // ===== Kategori =====
        $kat = [
            'oli'     => Category::create(['name' => 'Oli'])->id,
            'ban'     => Category::create(['name' => 'Ban & Velg'])->id,
            'rem'     => Category::create(['name' => 'Kampas Rem'])->id,
            'busi'    => Category::create(['name' => 'Busi'])->id,
            'filter'  => Category::create(['name' => 'Filter'])->id,
            'lainnya' => Category::create(['name' => 'Lainnya'])->id,
        ];

        // ===== Produk =====
        $produk = [
            ['name' => 'Yamahalube SuperSport 1L', 'sku' => '#120/YD990',  'price' => 50000,   'stock' => 12, 'cat' => 'oli'],
            ['name' => 'ShockBreaker Yamaha',       'sku' => '#120/YD790',  'price' => 150000,  'stock' => 5,  'cat' => 'lainnya'],
            ['name' => 'Kampas Rem Honda PCX',      'sku' => '#120/YD980',  'price' => 25000,   'stock' => 12, 'cat' => 'rem'],
            ['name' => 'Discbrake 777 220mm',       'sku' => '#120/JN2340', 'price' => 350000,  'stock' => 16, 'cat' => 'rem'],
            ['name' => 'Headlamp Yamaha Nmax 155',  'sku' => '#120/BG750',  'price' => 2500000, 'stock' => 5,  'cat' => 'lainnya'],
            ['name' => 'Baut Set Warna Titanium',   'sku' => '#120/BT990',  'price' => 25000,   'stock' => 50, 'cat' => 'lainnya'],
            ['name' => 'Ban Tubeless IRC 90/80',    'sku' => '#120/BN110',  'price' => 275000,  'stock' => 8,  'cat' => 'ban'],
            ['name' => 'Velg Racing Nmax',          'sku' => '#120/VG220',  'price' => 1200000, 'stock' => 3,  'cat' => 'ban'],
            ['name' => 'Busi NGK Iridium',          'sku' => '#120/BS330',  'price' => 85000,   'stock' => 30, 'cat' => 'busi'],
            ['name' => 'Busi Denso Standard',       'sku' => '#120/BS340',  'price' => 45000,   'stock' => 22, 'cat' => 'busi'],
            ['name' => 'Filter Oli Yamaha',         'sku' => '#120/FL440',  'price' => 35000,   'stock' => 24, 'cat' => 'filter'],
            ['name' => 'Oli Shell Advance AX7',     'sku' => '#120/OL550',  'price' => 65000,   'stock' => 18, 'cat' => 'oli'],
        ];

        foreach ($produk as $p) {
            Product::create([
                'category_id' => $kat[$p['cat']],
                'sku'         => $p['sku'],
                'name'        => $p['name'],
                'price'       => $p['price'],
                'stock'       => $p['stock'],
            ]);
        }
    }
}