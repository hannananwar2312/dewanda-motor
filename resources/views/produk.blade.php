@extends('layouts.app')

@section('title', 'Produk & Stok · Dewanda Motor')

@section('content')

<div x-data="produk" class="flex h-screen overflow-hidden bg-gray-50">

    <div x-show="sidebarOpen" x-cloak x-on:click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col justify-between border-r border-gray-200 bg-white p-3 transition-transform duration-300 lg:static lg:z-auto lg:w-52 lg:translate-x-0 lg:transition-none"
        :class="sidebarOpen && 'translate-x-0'">
        <div>
            <div class="mb-4 flex items-center justify-between px-1">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-600">
                        <i data-lucide="bike" class="h-5 w-5 text-white"></i>
                    </div>
                    <span class="text-base font-bold">Dewanda Motor</span>
                </div>
                <button x-on:click="sidebarOpen = false" class="text-gray-400 hover:text-gray-600 lg:hidden">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <nav class="space-y-1">
                <a href="/kasir" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="shopping-cart" class="h-5 w-5"></i> Kasir
                </a>
                @if (session('role') === 'admin')
                <a href="/produk" class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
                    <i data-lucide="boxes" class="h-5 w-5"></i> Produk & Stok
                </a>
                @endif
                <a href="/insight" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="sparkles" class="h-5 w-5"></i> AI Insight
                </a>
                @if (session('role') === 'admin')
                <a href="/pembukuan" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="book-open" class="h-5 w-5"></i> Pembukuan
                </a>
                @endif
            </nav>
        </div>

        <div class="flex items-center gap-3 px-2 py-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-800 text-sm font-bold text-white">
                {{ strtoupper(substr($user, 0, 1)) }}
            </div>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-sm font-medium text-red-600 hover:text-red-700">
                    <i data-lucide="log-out" class="h-4 w-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-gray-200 bg-white/90 px-4 backdrop-blur lg:px-6">
            <button x-on:click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 lg:hidden">
                <i data-lucide="menu" class="h-6 w-6"></i>
            </button>
            <div>
                <h2 class="text-lg font-bold">Produk & Stok</h2>
                <p class="hidden text-xs text-gray-400 sm:block">Kelola produk, kategori, dan stok barang.</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button x-on:click="openKategori()"
                    class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i data-lucide="tag" class="h-4 w-4"></i> <span class="hidden sm:inline">Tambah Kategori</span>
                </button>
                <button x-on:click="openAdd()"
                    class="flex items-center gap-2 rounded-xl bg-red-600 px-3 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    <i data-lucide="plus" class="h-4 w-4"></i> <span class="hidden sm:inline">Tambah Produk</span>
                </button>
            </div>
        </header>

        <div class="p-4 lg:p-6">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                    <input x-model="query" placeholder="Cari nama produk atau SKU…"
                        class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-11 pr-4 text-sm outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100">
                </div>
                <select x-model="filterCat"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-red-300">
                    <option value="semua">Semua Kategori</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c['code'] }}">{{ $c['emoji'] }} {{ $c['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Total Produk</p>
                    <p class="mt-1 text-xl font-bold" x-text="products.length"></p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Stok Menipis</p>
                    <p class="mt-1 text-xl font-bold text-amber-600" x-text="products.filter(p => p.status === 'menipis').length"></p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Stok Habis</p>
                    <p class="mt-1 text-xl font-bold text-red-600" x-text="products.filter(p => p.status === 'habis').length"></p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Nilai Stok</p>
                    <p class="mt-1 text-xl font-bold" x-text="rupiah(products.reduce((s, p) => s + p.price * p.stock, 0))"></p>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400">
                                <th class="px-4 py-3 font-medium">Produk</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Stok</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="p in filtered()" :key="p.id">
                                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-xl text-lg" :class="p.tint">
                                                <span x-text="p.emoji"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-gray-900" x-text="p.name"></p>
                                                <p class="text-xs text-gray-400">SKU : <span x-text="p.sku"></span></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600" x-text="p.cat_name"></td>
                                    <td class="px-4 py-3 font-medium text-gray-900" x-text="rupiah(p.price)"></td>
                                    <td class="px-4 py-3 font-semibold" x-text="p.stock"></td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="{
                                                'bg-green-50 text-green-600': p.status === 'tersedia',
                                                'bg-amber-50 text-amber-600': p.status === 'menipis',
                                                'bg-red-50 text-red-600': p.status === 'habis',
                                            }"
                                            x-text="p.status === 'tersedia' ? 'Tersedia' : (p.status === 'menipis' ? 'Menipis' : 'Habis')"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <button x-on:click="openBeli(p)" title="Beli / Tambah Stok"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-green-50 hover:text-green-600">
                                                <i data-lucide="package-plus" class="h-4 w-4"></i>
                                            </button>
                                            <button x-on:click="openEdit(p)" title="Edit"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600">
                                                <i data-lucide="pencil" class="h-4 w-4"></i>
                                            </button>
                                            <button x-on:click="hapus(p)" title="Hapus"
                                                class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div x-show="filtered().length === 0" class="py-16 text-center">
                    <p class="text-sm text-gray-500">Produk tidak ditemukan.</p>
                </div>
            </div>
        </div>
    </main>

    <div x-show="toast" x-cloak
        class="pointer-events-none fixed left-1/2 top-4 z-[70] -translate-x-1/2 rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-lg"
        x-text="toast"></div>

    <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold" x-text="form.id ? 'Edit Produk' : 'Tambah Produk'"></h3>
                <button x-on:click="showForm = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-gray-500">Nama Produk</label>
                    <input x-model="form.name" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-medium text-gray-500">SKU / Barcode</label>
                    <input x-model="form.sku" x-ref="skuInput" placeholder="Scan atau ketik barcode…"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Kategori</label>
                    <select x-model="form.cat" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400">
                        @foreach ($categories as $c)
                            <option value="{{ $c['code'] }}">{{ $c['emoji'] }} {{ $c['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Stok Awal</label>
                    <input type="number" x-model="form.stock" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Harga Jual</label>
                    <input type="number" x-model="form.price" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Harga Beli (modal)</label>
                    <input type="number" x-model="form.cost" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button x-on:click="showForm = false" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button x-on:click="saveProduk()" :disabled="saving" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:bg-gray-300">Simpan</button>
            </div>
        </div>
    </div>

    <div x-show="showKategori" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold">Tambah Kategori</h3>
                <button x-on:click="showKategori = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Nama Kategori</label>
            <input x-model="kat.name" placeholder="cth: Lampu, Aki, Rantai…"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

            <label class="mb-1 mt-4 block text-xs font-medium text-gray-500">Ikon (emoji)</label>
            <div class="flex flex-wrap gap-2">
                <template x-for="e in emojiPicks" :key="e">
                    <button x-on:click="kat.emoji = e"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border text-xl"
                        :class="kat.emoji === e ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'"
                        x-text="e"></button>
                </template>
            </div>

            <label class="mb-1 mt-4 block text-xs font-medium text-gray-500">Warna</label>
            <div class="flex flex-wrap gap-2">
                <template x-for="t in tints" :key="t">
                    <button x-on:click="kat.tint = t"
                        class="h-9 w-9 rounded-xl border-2"
                        :class="[t, kat.tint === t ? 'border-red-500' : 'border-transparent']"></button>
                </template>
            </div>

            <div class="mt-5 flex gap-3">
                <button x-on:click="showKategori = false" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button x-on:click="saveKategori()" :disabled="saving" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:bg-gray-300">Simpan</button>
            </div>
        </div>
    </div>

    <div x-show="showBeli" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold">Beli / Tambah Stok</h3>
                <button x-on:click="showBeli = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <p class="mb-4 rounded-xl bg-gray-50 px-3 py-2 text-sm">
                <span class="font-semibold" x-text="beliProduk.name"></span>
                <span class="text-gray-400"> · stok saat ini <span x-text="beliProduk.stock"></span></span>
            </p>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Jumlah Beli</label>
                    <input type="number" x-model="beli.qty" min="1" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Harga Beli / pcs</label>
                    <input type="number" x-model="beli.buy_price" min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
            </div>

            <label class="mb-1 mt-3 block text-xs font-medium text-gray-500">Supplier</label>
            <div class="flex gap-2">
                <select x-model="beli.supplier" class="flex-1 rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400">
                    <option value="">— Tanpa supplier —</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s['name'] }}">{{ $s['name'] }}</option>
                    @endforeach
                </select>
                <button x-on:click="openSupplier()" class="shrink-0 rounded-xl border border-gray-200 px-3 text-sm font-semibold text-gray-600 hover:bg-gray-50">+ Baru</button>
            </div>

            <div class="mt-4 flex justify-between rounded-xl bg-gray-50 px-3 py-2 text-sm">
                <span class="text-gray-500">Total Beli</span>
                <span class="font-bold" x-text="rupiah((beli.qty || 0) * (beli.buy_price || 0))"></span>
            </div>

            <div class="mt-5 flex gap-3">
                <button x-on:click="showBeli = false" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button x-on:click="saveBeli()" :disabled="saving" class="flex-1 rounded-xl bg-green-600 py-2.5 text-sm font-semibold text-white hover:bg-green-700 disabled:bg-gray-300">Simpan</button>
            </div>
        </div>
    </div>

    <div x-show="showSupplier" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold">Tambah Supplier</h3>
                <button x-on:click="showSupplier = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Nama Supplier</label>
            <input x-model="sup.name" placeholder="cth: Toko Jaya Motor"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
            <div class="mt-5 flex gap-3">
                <button x-on:click="showSupplier = false" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button x-on:click="saveSupplier()" :disabled="saving" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:bg-gray-300">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('produk', () => ({
            products: @json($rows),
            categories: @json($categories),
            suppliers: @json($suppliers),
            csrf: '{{ csrf_token() }}',

            sidebarOpen: false,
            query: '',
            filterCat: 'semua',
            saving: false,
            toast: '',

            showForm: false,
            form: { id: null, sku: '', name: '', cat: '', stock: 0, price: 0, cost: 0, image: null },

            showKategori: false,
            kat: { name: '', emoji: '📦', tint: 'bg-gray-100' },
            emojiPicks: ['📦','🛢️','🛞','🛑','⚡','🌀','🔋','💡','⛓️','🔧','🪛','🧰'],
            tints: ['bg-gray-100','bg-blue-50','bg-zinc-100','bg-slate-100','bg-orange-50','bg-emerald-50','bg-amber-50','bg-rose-50'],

            showSupplier: false,
            sup: { name: '' },

            showBeli: false,
            beliProduk: { name: '', stock: 0 },
            beli: { product_id: null, qty: 1, buy_price: 0, supplier: '' },

            _scanBuf: '',
            _scanTime: 0,

            init() {
                window.addEventListener('keydown', (e) => {
                    const el = document.activeElement;
                    const typing = el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT');
                    if (typing && el !== this.$refs.skuInput) return;

                    const now = Date.now();
                    if (now - this._scanTime > 120) this._scanBuf = '';
                    this._scanTime = now;

                    if (e.key === 'Enter') {
                        const code = this._scanBuf.trim();
                        this._scanBuf = '';
                        if (code.length >= 3) {
                            e.preventDefault();
                            this.scanCode(code);
                        }
                        return;
                    }
                    if (e.key.length === 1) this._scanBuf += e.key;
                });
            },
            scanCode(code) {
                const norm = s => (s || '').toLowerCase().replace(/[^a-z0-9]/g, '');
                const key = norm(code);
                const p = this.products.find(x => norm(x.sku) === key);
                if (p) {
                    this.openEdit(p);
                    this.showToast('Produk ditemukan — mode edit');
                } else {
                    this.openAdd();
                    this.form.sku = code;
                    this.showToast('Barcode baru — isi detail produk');
                }
            },
            showToast(msg) {
                this.toast = msg;
                clearTimeout(this._toastT);
                this._toastT = setTimeout(() => this.toast = '', 2000);
            },

            filtered() {
                const q = this.query.trim().toLowerCase();
                return this.products.filter(p =>
                    (this.filterCat === 'semua' || p.cat === this.filterCat) &&
                    (!q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q))
                );
            },
            rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },

            openAdd() {
                this.form = { id: null, sku: '', name: '', cat: (this.categories[0]?.code || ''), stock: 0, price: 0, cost: 0, image: null };
                this.showForm = true;
            },
            openEdit(p) {
                this.form = { id: p.id, sku: p.sku, name: p.name, cat: p.cat, stock: p.stock, price: p.price, cost: 0, image: p.image };
                this.showForm = true;
            },
            async saveProduk() {
                if (!this.form.name || !this.form.sku) { alert('Nama dan SKU wajib diisi.'); return; }
                this.saving = true;
                try {
                    const res = await fetch('/produk/simpan', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            id: this.form.id,
                            sku: this.form.sku,
                            name: this.form.name,
                            cat: this.form.cat,
                            stock: Number(this.form.stock) || 0,
                            price: Number(this.form.price) || 0,
                            cost: Number(this.form.cost) || 0,
                            image: this.form.image,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menyimpan produk.'); this.saving = false; return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); this.saving = false; }
            },
            async hapus(p) {
                if (!confirm('Hapus produk "' + p.name + '"?')) return;
                try {
                    const res = await fetch('/produk/hapus', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ id: p.id }),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menghapus.'); return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); }
            },

            openKategori() {
                this.kat = { name: '', emoji: '📦', tint: 'bg-gray-100' };
                this.showKategori = true;
            },
            async saveKategori() {
                if (!this.kat.name.trim()) { alert('Nama kategori wajib diisi.'); return; }
                this.saving = true;
                try {
                    const res = await fetch('/produk/kategori', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify(this.kat),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menambah kategori.'); this.saving = false; return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); this.saving = false; }
            },

            openSupplier() {
                this.sup = { name: '' };
                this.showSupplier = true;
            },
            async saveSupplier() {
                if (!this.sup.name.trim()) { alert('Nama supplier wajib diisi.'); return; }
                this.saving = true;
                try {
                    const res = await fetch('/produk/supplier', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify(this.sup),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menambah supplier.'); this.saving = false; return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); this.saving = false; }
            },

            openBeli(p) {
                this.beliProduk = { name: p.name, stock: p.stock };
                this.beli = { product_id: p.id, qty: 1, buy_price: 0, supplier: '' };
                this.showBeli = true;
            },
            async saveBeli() {
                if (!this.beli.qty || this.beli.qty < 1) { alert('Jumlah beli minimal 1.'); return; }
                this.saving = true;
                try {
                    const res = await fetch('/produk/beli', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            product_id: this.beli.product_id,
                            qty: Number(this.beli.qty) || 0,
                            buy_price: Number(this.beli.buy_price) || 0,
                            supplier: this.beli.supplier || null,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menyimpan pembelian.'); this.saving = false; return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); this.saving = false; }
            },
        }));
    });
</script>
@endpush