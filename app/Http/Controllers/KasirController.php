<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KasirController extends Controller
{
    // Map nama kategori (dari DB) -> kode singkat + emoji + warna untuk tampilan
    private function catMeta(string $name): array
    {
        $map = [
            'Oli'        => ['cat' => 'oli',     'emoji' => '🛢️', 'tint' => 'bg-blue-50'],
            'Ban & Velg' => ['cat' => 'ban',     'emoji' => '🛞', 'tint' => 'bg-zinc-100'],
            'Kampas Rem' => ['cat' => 'rem',     'emoji' => '🛑', 'tint' => 'bg-slate-100'],
            'Busi'       => ['cat' => 'busi',    'emoji' => '⚡', 'tint' => 'bg-orange-50'],
            'Filter'     => ['cat' => 'filter',  'emoji' => '🌀', 'tint' => 'bg-emerald-50'],
            'Lainnya'    => ['cat' => 'lainnya', 'emoji' => '📦', 'tint' => 'bg-gray-100'],
        ];
        return $map[$name] ?? ['cat' => 'lainnya', 'emoji' => '📦', 'tint' => 'bg-gray-100'];
    }

    // Ambil produk dari DB, ubah ke bentuk array yang dipakai front end
    private function products(): array
    {
        return Product::with('category')->orderBy('name')->get()->map(function ($p) {
            $meta = $this->catMeta(optional($p->category)->name ?? 'Lainnya');
            return [
                'id'    => $p->id,
                'name'  => $p->name,
                'sku'   => $p->sku,
                'price' => (int) $p->price,
                'stock' => (int) $p->stock,
                'sold'  => (int) $p->sales_qty, // jumlah terjual (dihitung di bawah)
                'cat'   => $meta['cat'],
                'emoji' => $meta['emoji'],
                'tint'  => $meta['tint'],
                'image' => $p->image,
            ];
        })->all();
    }

    // Produk + kolom sales_qty (total terjual dari tabel sale_items)
    private function productsWithSold()
    {
        return Product::with('category')
            ->withSum('saleItems as sales_qty', 'qty')
            ->orderBy('name')
            ->get();
    }

    public function showLogin()
    {
        if (session('role')) {
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

        $user = User::where('username', strtolower($data['username']))->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            session([
                'user_id' => $user->id,
                'user'    => $user->username,
                'role'    => $user->role,
            ]);
            return redirect('/kasir');
        }

        return back()
            ->withErrors(['login' => 'Username atau password salah.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['user_id', 'user', 'role']);
        return redirect('/login');
    }

    public function index()
    {
        if (!session('role')) {
            return redirect('/login');
        }

        // Produk + jumlah terjual
        $products = $this->productsWithSold()->map(function ($p) {
            $meta = $this->catMeta(optional($p->category)->name ?? 'Lainnya');
            return [
                'id' => $p->id, 'name' => $p->name, 'sku' => $p->sku,
                'price' => (int) $p->price, 'stock' => (int) $p->stock,
                'sold' => (int) ($p->sales_qty ?? 0),
                'cat' => $meta['cat'], 'emoji' => $meta['emoji'], 'tint' => $meta['tint'],
                'image' => $p->image,
            ];
        })->all();

        return view('kasir', [
            'products' => $products,
            'user'     => session('user'),
            'invoice'  => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) rand(100, 999), 3, '0', STR_PAD_LEFT),
        ]);
    }

    public function insight()
    {
        if (!session('role')) {
            return redirect('/login');
        }

        $products = $this->productsWithSold();

        $data = $products->map(function ($p) {
            $meta = $this->catMeta(optional($p->category)->name ?? 'Lainnya');
            return [
                'name' => $p->name, 'sku' => $p->sku, 'price' => (int) $p->price,
                'stock' => (int) $p->stock, 'sold' => (int) ($p->sales_qty ?? 0),
                'emoji' => $meta['emoji'], 'tint' => $meta['tint'],
            ];
        });

        $terlaris = $data->sortByDesc('sold')->take(5)->values()->all();
        $menipis  = $data->filter(fn ($p) => $p['stock'] <= 8)->sortBy('stock')->values()->all();

        $totalProduk  = $data->count();
        $totalTerjual = $data->sum('sold');
        $totalOmzet   = $data->sum(fn ($p) => $p['price'] * $p['sold']);
        $nilaiStok    = $data->sum(fn ($p) => $p['price'] * $p['stock']);

        $top    = $terlaris[0] ?? null;
        $kritis = $menipis[0] ?? null;

        if ($totalTerjual == 0) {
            $ringkasan = 'Belum ada transaksi penjualan yang tercatat. ';
        } else {
            $ringkasan = 'Produk terlaris saat ini adalah ' . ($top['name'] ?? '-') . ' dengan ' . ($top['sold'] ?? 0) . ' unit terjual. ';
        }
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
        if (!session('role')) {
            return redirect('/login');
        }
        if (session('role') !== 'admin') {
            return redirect('/kasir');
        }

        // Riwayat penjualan dari DB
        $rows = Sale::with('items.product')->orderByDesc('sale_date')->orderByDesc('id')->get()
            ->flatMap(function ($s) {
                return $s->items->map(fn ($it) => [
                    'tanggal'    => $s->sale_date,
                    'invoice'    => $s->invoice,
                    'produk'     => optional($it->product)->name ?? '-',
                    'jumlah'     => $it->qty,
                    'harga'      => (int) $it->price,
                    'total'      => (int) $it->subtotal,
                    'pembayaran' => strtolower($s->payment_method) === 'qris' ? 'QRIS' : 'Cash',
                ]);
            })->values()->all();

        // Riwayat pembelian dari DB
        $pembelian = Purchase::with(['items.product', 'supplier'])->orderByDesc('purchase_date')->orderByDesc('id')->get()
            ->flatMap(function ($pu) {
                return $pu->items->map(fn ($it) => [
                    'tanggal'  => $pu->purchase_date,
                    'faktur'   => $pu->reference,
                    'produk'   => optional($it->product)->name ?? '-',
                    'jumlah'   => $it->qty,
                    'harga'    => (int) $it->buy_price,
                    'total'    => (int) $it->subtotal,
                    'supplier' => optional($pu->supplier)->name ?? '-',
                ]);
            })->values()->all();

        return view('pembukuan', [
            'user'      => session('user'),
            'rows'      => $rows,
            'pembelian' => $pembelian,
        ]);
    }

    public function produk()
    {
        if (!session('role')) {
            return redirect('/login');
        }
        if (session('role') !== 'admin') {
            return redirect('/kasir');
        }

        $statusOf = function ($stock) {
            if ($stock <= 0) return 'habis';
            if ($stock <= 8) return 'menipis';
            return 'tersedia';
        };

        $rows = Product::with('category')->orderBy('name')->get()->map(function ($p) use ($statusOf) {
            $meta = $this->catMeta(optional($p->category)->name ?? 'Lainnya');
            return [
                'id' => $p->id, 'name' => $p->name, 'sku' => $p->sku,
                'price' => (int) $p->price, 'stock' => (int) $p->stock,
                'cat' => $meta['cat'], 'emoji' => $meta['emoji'], 'tint' => $meta['tint'],
                'image' => $p->image, 'status' => $statusOf((int) $p->stock),
            ];
        })->all();

        return view('produk', [
            'user' => session('user'),
            'rows' => $rows,
        ]);
    }

        public function simpanTransaksi(Request $request)
    {
        if (!session('role')) {
            return response()->json(['message' => 'Sesi berakhir, silakan login ulang.'], 401);
        }

        $data = $request->validate([
            'payment_method' => 'required|in:cash,qris',
            'cash_received'  => 'nullable|numeric',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|integer',
            'items.*.qty'    => 'required|integer|min:1',
        ]);

        try {
            $sale = \DB::transaction(function () use ($data) {
                $subtotal = 0;
                $rincian  = [];

                foreach ($data['items'] as $item) {
                    $product = Product::lockForUpdate()->find($item['id']);
                    if (!$product) {
                        throw new \Exception('Produk tidak ditemukan.');
                    }
                    if ($product->stock < $item['qty']) {
                        throw new \Exception('Stok "' . $product->name . '" tidak cukup (sisa ' . $product->stock . ').');
                    }
                    $sub = (int) $product->price * (int) $item['qty'];
                    $subtotal += $sub;
                    $rincian[] = ['product' => $product, 'qty' => (int) $item['qty'], 'price' => (int) $product->price, 'subtotal' => $sub];
                }

                $discount = 0;
                $tax      = (int) round(($subtotal - $discount) * 0.11);
                $total    = $subtotal - $discount + $tax;

                $cash   = $data['payment_method'] === 'cash' ? (int) ($data['cash_received'] ?? 0) : null;
                if ($data['payment_method'] === 'cash' && $cash < $total) {
                    throw new \Exception('Uang tunai kurang dari total.');
                }
                $change = $data['payment_method'] === 'cash' ? max(0, $cash - $total) : null;

                $invoice = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) (Sale::whereDate('sale_date', today())->count() + 1), 3, '0', STR_PAD_LEFT);

                $sale = Sale::create([
                    'user_id'        => session('user_id'),
                    'invoice'        => $invoice,
                    'sale_date'      => today(),
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'tax'            => $tax,
                    'total'          => $total,
                    'payment_method' => $data['payment_method'],
                    'cash_received'  => $cash,
                    'change_amount'  => $change,
                ]);

                foreach ($rincian as $r) {
                    $sale->items()->create([
                        'product_id' => $r['product']->id,
                        'qty'        => $r['qty'],
                        'price'      => $r['price'],
                        'subtotal'   => $r['subtotal'],
                    ]);
                    $r['product']->decrement('stock', $r['qty']);
                }

                return $sale;
            });

            return response()->json([
                'invoice' => $sale->invoice,
                'tanggal' => now()->format('d/m/Y, H.i.s'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

        // ubah kode kategori (oli/ban/dst) -> id kategori di database
    private function categoryIdByCode(string $code): ?int
    {
        $names = [
            'oli' => 'Oli', 'ban' => 'Ban & Velg', 'rem' => 'Kampas Rem',
            'busi' => 'Busi', 'filter' => 'Filter', 'lainnya' => 'Lainnya',
        ];
        $name = $names[$code] ?? 'Lainnya';
        return \App\Models\Category::firstOrCreate(['name' => $name])->id;
    }

    public function simpanProduk(Request $request)
    {
        if (session('role') !== 'admin') {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $data = $request->validate([
            'id'    => 'nullable|integer',
            'sku'   => ['required', 'string', \Illuminate\Validation\Rule::unique('products', 'sku')->ignore($request->id)],
            'name'  => 'required|string',
            'cat'   => 'required|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'image' => 'nullable|string',
        ]);

        $payload = [
            'category_id' => $this->categoryIdByCode($data['cat']),
            'sku'         => $data['sku'],
            'name'        => $data['name'],
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'image'       => $data['image'] ?? null,
        ];

        if (!empty($data['id'])) {
            Product::where('id', $data['id'])->update($payload);
        } else {
            Product::create($payload);
        }

        return response()->json(['ok' => true]);
    }

    public function hapusProduk(Request $request)
    {
        if (session('role') !== 'admin') {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $data = $request->validate(['id' => 'required|integer']);
        Product::where('id', $data['id'])->delete();

        return response()->json(['ok' => true]);
    }

        public function simpanPembelian(Request $request)
    {
        if (session('role') !== 'admin') {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $data = $request->validate([
            'product_id' => 'required|integer',
            'qty'        => 'required|integer|min:1',
            'buy_price'  => 'required|integer|min:0',
            'supplier'   => 'nullable|string',
        ]);

        try {
            \DB::transaction(function () use ($data) {
                $product = Product::lockForUpdate()->findOrFail($data['product_id']);

                $supplierId = null;
                if (!empty($data['supplier'])) {
                    $supplierId = \App\Models\Supplier::firstOrCreate(['name' => $data['supplier']])->id;
                }

                $total = (int) $data['buy_price'] * (int) $data['qty'];

                $purchase = Purchase::create([
                    'user_id'       => session('user_id'),
                    'supplier_id'   => $supplierId,
                    'reference'     => 'PO-' . now()->format('Ymd') . '-' . str_pad((string) (Purchase::whereDate('purchase_date', today())->count() + 1), 3, '0', STR_PAD_LEFT),
                    'purchase_date' => today(),
                    'total'         => $total,
                ]);

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'qty'        => (int) $data['qty'],
                    'buy_price'  => (int) $data['buy_price'],
                    'subtotal'   => $total,
                ]);

                $product->increment('stock', (int) $data['qty']); // stok bertambah
            });

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}