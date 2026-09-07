@extends('layouts.app')

@section('title', 'Produk & Stok · Dewanda Motor')

@section('content')
<div x-data="produk()" class="flex h-screen flex-col overflow-hidden bg-gray-50">

    <header class="flex h-16 shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4">
        <div class="flex w-52 items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-600">
                <i data-lucide="bike" class="h-5 w-5 text-white"></i>
            </div>
            <span class="text-lg font-bold">Dewanda Motor</span>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        <aside class="flex w-52 shrink-0 flex-col justify-between border-r border-gray-200 bg-white p-3">

            <nav class="space-y-1">
                <a href="/kasir"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                    Kasir
                </a>

                <a href="/produk"
                    class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
                    <i data-lucide="boxes" class="h-5 w-5"></i>
                    Produk & Stok
                </a>

                <a href="/insight"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="sparkles" class="h-5 w-5"></i>
                    AI Insight
                </a>

                <a href="/pembukuan"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="book-open" class="h-5 w-5"></i>
                    Pembukuan
                </a>
            </nav>

            <div class="flex items-center gap-3 px-2 py-2">
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-800 text-sm font-bold text-white">
                    {{ strtoupper(substr($user, 0, 1)) }}
                </div>

                <form method="POST" action="/logout">
                    @csrf

                    <button type="submit"
                        class="flex items-center gap-2 text-sm font-medium text-red-600 hover:text-red-700">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        Logout
                    </button>
                </form>
            </div>

        </aside>

        <main class="flex-1 overflow-y-auto p-6">

            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Produk & Stok</h2>
                    <p class="text-sm text-gray-400">Kelola Data dan Stok Produk</p>
                </div>

                <button x-on:click="openAdd()"
                    class="flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tambah Produk
                </button>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500">
                        <i data-lucide="package" class="h-4 w-4"></i>
                        <span class="text-xs">Total Produk</span>
                    </div>

                    <p class="text-2xl font-bold" x-text="rows.length"></p>
                    <p class="text-xs text-gray-400">Semua Produk</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500">
                        <i data-lucide="check-circle" class="h-4 w-4 text-green-600"></i>
                        <span class="text-xs">Stok Tersedia</span>
                    </div>

                    <p class="text-2xl font-bold" x-text="countStatus('tersedia')"></p>
                    <p class="text-xs text-gray-400">Stok Aman</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500">
                        <i data-lucide="alert-circle" class="h-4 w-4 text-amber-500"></i>
                        <span class="text-xs">Stok Menipis</span>
                    </div>

                    <p class="text-2xl font-bold" x-text="countStatus('menipis')"></p>
                    <p class="text-xs text-gray-400">Perlu restock</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500">
                        <i data-lucide="x-circle" class="h-4 w-4 text-red-600"></i>
                        <span class="text-xs">Stok Habis</span>
                    </div>

                    <p class="text-2xl font-bold" x-text="countStatus('habis')"></p>
                    <p class="text-xs text-gray-400">Segera restock</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500">
                        <i data-lucide="dollar-sign" class="h-4 w-4"></i>
                        <span class="text-xs">Total Nilai Stok</span>
                    </div>

                    <p class="text-2xl font-bold" x-text="rupiah(nilaiStok())"></p>
                    <p class="text-xs text-gray-400">Nilai Stok</p>
                </div>

            </div>

            <div class="mb-4 flex flex-col gap-3 md:flex-row">

                <div class="relative flex-1">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>

                    <input x-model="query"
                        placeholder="Cari produk (Nama/Kode)"
                        class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-3 text-sm outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100">
                </div>

                <select x-model="kategori"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-red-300 md:w-48">
                    <option value="semua">Semua Kategori</option>

                    <template x-for="k in kategoriList()" :key="k">
                        <option :value="k" x-text="k"></option>
                    </template>
                </select>

                <select x-model="statusFilter"
                    class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-red-300 md:w-44">
                    <option value="semua">Semua Status</option>
                    <option value="tersedia">Stok Tersedia</option>
                    <option value="menipis">Stok Menipis</option>
                    <option value="habis">Stok Habis</option>
                </select>

            </div>

            <div class="rounded-2xl border border-gray-200 bg-white">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-left text-xs text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Kode</th>
                                <th class="px-4 py-3 font-medium">Gambar</th>
                                <th class="px-4 py-3 font-medium">Nama</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                                <th class="px-4 py-3 font-medium">Stok</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            <template x-for="p in filtered()" :key="p.id">

                                <tr class="hover:bg-gray-50">

                                    <td class="px-4 py-3 text-gray-500" x-text="p.sku"></td>

                                    <td class="px-4 py-3">

                                        <template x-if="p.image">
                                            <img :src="p.image"
                                                class="h-10 w-10 rounded-lg object-cover"
                                                alt="">
                                        </template>

                                        <template x-if="!p.image">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-lg text-xl"
                                                :class="p.tint"
                                                x-text="p.emoji">
                                            </div>
                                        </template>

                                    </td>

                                    <td class="px-4 py-3 font-medium" x-text="p.name"></td>

                                    <td class="px-4 py-3" x-text="labelKategori(p.cat)"></td>

                                    <td class="px-4 py-3" x-text="p.stock"></td>

                                    <td class="px-4 py-3" x-text="rupiah(p.price)"></td>

                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-md px-2.5 py-1 text-xs font-semibold"
                                            :class="badge(p.status)"
                                            x-text="labelStatus(p.status)">
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">

                                        <div class="flex gap-2">

                                            <button
                                                x-on:click="openBeli(p)"
                                                class="rounded-md bg-green-600 px-3 py-1 text-xs font-semibold text-white hover:bg-green-700">
                                                Beli
                                            </button>

                                            <button
                                                x-on:click="openEdit(p)"
                                                class="rounded-md bg-blue-500 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-600">
                                                Update
                                            </button>

                                            <button
                                                x-on:click="askDelete(p)"
                                                class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">
                                                Delete
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            </template>

                            <tr x-show="filtered().length === 0">
                                <td colspan="8"
                                    class="px-4 py-10 text-center text-sm text-gray-400">
                                    Produk tidak ditemukan.
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <div
                    class="border-t border-gray-100 p-4 text-xs text-gray-500"
                    x-text="'Menampilkan ' + filtered().length + ' dari ' + rows.length + ' data'">
                </div>

            </div>

        </main>

    </div>


    {{-- ===== MODAL TAMBAH / UPDATE PRODUK ===== --}}

    <div x-show="showForm" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">

        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

            <div class="mb-4 flex items-center justify-between">

                <h3 class="text-lg font-bold"
                    x-text="mode === 'add' ? 'Tambah Produk' : 'Update Produk'">
                </h3>

                <button x-on:click="showForm = false"
                    class="text-gray-400 hover:text-gray-600">

                    <i data-lucide="x" class="h-5 w-5"></i>

                </button>

            </div>

            <div class="space-y-3">

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Kode / SKU
                    </label>

                    <input
                        x-model="form.sku"
                        placeholder="#120/XX000"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Nama Produk
                    </label>

                    <input
                        x-model="form.name"
                        placeholder="Nama produk"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Kategori
                    </label>

                    <select
                        x-model="form.cat"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

                        <option value="oli">Oli</option>
                        <option value="ban">Ban & Velg</option>
                        <option value="rem">Kampas Rem</option>
                        <option value="busi">Busi</option>
                        <option value="filter">Filter</option>
                        <option value="lainnya">Lainnya</option>

                    </select>
                </div>

                <div>

                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Gambar Produk
                    </label>

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50 text-2xl">

                            <template x-if="form.image">
                                <img
                                    :src="form.image"
                                    class="h-full w-full object-cover"
                                    alt="preview">
                            </template>

                            <template x-if="!form.image">
                                <span x-text="emojiByCat(form.cat)"></span>
                            </template>

                        </div>

                        <div class="flex-1">

                            <input
                                type="file"
                                accept="image/*"
                                x-on:change="pickImage($event)"
                                class="block w-full text-xs text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-red-600 hover:file:bg-red-100">

                            <button
                                type="button"
                                x-show="form.image"
                                x-on:click="form.image = ''"
                                class="mt-1 text-xs text-gray-400 hover:text-red-500">
                                Hapus gambar
                            </button>

                        </div>

                    </div>

                </div>

                <div class="grid grid-cols-2 gap-3">

                    <div>

                        <label class="mb-1 block text-xs font-medium text-gray-500">
                            Stok
                        </label>

                        <input
                            type="number"
                            min="0"
                            x-model.number="form.stock"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

                    </div>

                    <div>

                        <label class="mb-1 block text-xs font-medium text-gray-500">
                            Harga (Rp)
                        </label>

                        <input
                            type="text"
                            inputmode="numeric"
                            :value="formatRibuan(form.price)"
                            x-on:input="form.price = angkaSaja($event.target.value)"
                            placeholder="0"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

                    </div>

                </div>

                <p
                    x-show="error"
                    x-cloak
                    class="text-sm text-red-600"
                    x-text="error">
                </p>

            </div>

            <div class="mt-5 flex gap-3">

                <button
                    x-on:click="showForm = false"
                    class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </button>

                <button
                    x-on:click="saveProduk()"
                    class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    Simpan
                </button>

            </div>

        </div>

    </div>


    {{-- ===== MODAL BELI / RESTOCK ===== --}}

    <div
        x-show="showBeli"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">

        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

            <div class="mb-4 flex items-center justify-between">

                <h3 class="text-lg font-bold">
                    Beli / Restock Stok
                </h3>

                <button
                    x-on:click="showBeli = false"
                    class="text-gray-400 hover:text-gray-600">

                    <i data-lucide="x" class="h-5 w-5"></i>

                </button>

            </div>

            <div class="mb-3 rounded-xl bg-gray-50 p-3 text-sm">

                <p
                    class="font-semibold"
                    x-text="beli.name">
                </p>

                <p class="text-xs text-gray-400">
                    Stok saat ini:
                    <span x-text="beli.stock"></span>
                </p>

            </div>

            <div class="space-y-3">

                <div>

                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Jumlah Beli
                    </label>

                    <input
                        type="number"
                        min="1"
                        x-model.number="beli.qty"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

                </div>

                <div>

                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Harga Beli / unit (Rp)
                    </label>

                    <input
                        type="text"
                        inputmode="numeric"
                        :value="formatRibuan(beli.buy_price)"
                        x-on:input="beli.buy_price = angkaSaja($event.target.value)"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

                </div>

                <div>

                    <label class="mb-1 block text-xs font-medium text-gray-500">
                        Supplier
                    </label>

                    <input
                        x-model="beli.supplier"
                        placeholder="Nama supplier"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

                </div>

                <p
                    x-show="beliError"
                    x-cloak
                    class="text-sm text-red-600"
                    x-text="beliError">
                </p>

            </div>

            <div class="mt-5 flex gap-3">

                <button
                    x-on:click="showBeli = false"
                    class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </button>

                <button
                    x-on:click="simpanBeli()"
                    class="flex-1 rounded-xl bg-green-600 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                    Simpan Pembelian
                </button>

            </div>

        </div>

    </div>


    {{-- ===== MODAL KONFIRMASI DELETE ===== --}}

    <div
        x-show="showDelete"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">

        <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-xl">

            <div
                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-red-50">

                <i
                    data-lucide="trash-2"
                    class="h-6 w-6 text-red-600">
                </i>

            </div>

            <h3 class="text-lg font-bold">
                Hapus Produk?
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Yakin ingin menghapus
                <span
                    class="font-semibold"
                    x-text="target?.name">
                </span>?
                Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="mt-5 flex gap-3">

                <button
                    x-on:click="showDelete = false"
                    class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </button>

                <button
                    x-on:click="confirmDelete()"
                    class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    Hapus
                </button>

            </div>

        </div>

    </div>

</div>
@endsection


@push('scripts')

<script>

    document.addEventListener('alpine:init', () => {

        Alpine.data('produk', () => ({

            rows: @json($rows),

            query: '',
            kategori: 'semua',
            statusFilter: 'semua',

            csrf: '{{ csrf_token() }}',
            saving: false,
            showBeli: false,
            beliError: '',
            beli: { id: null, name: '', stock: 0, qty: 1, buy_price: 0, supplier: '' },


            // =========================
            // MODAL
            // =========================

            showForm: false,
            showDelete: false,
            showBeli: false,


            mode: 'add',

            error: '',
            beliError: '',

            editId: null,
            target: null,


            // =========================
            // DATA BELI / RESTOCK
            // =========================

            beli: {
                id: null,
                name: '',
                stock: 0,
                qty: 1,
                buy_price: 0,
                supplier: ''
            },


            // =========================
            // FORM PRODUK
            // =========================

            form: {
                sku: '',
                name: '',
                cat: 'oli',
                stock: 0,
                price: 0,
                image: ''
            },


            // =========================
            // FORMAT
            // =========================

            rupiah(n) {
                return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            },

            formatRibuan(n) {
                return Number(n || 0).toLocaleString('id-ID');
            },

            angkaSaja(s) {
                return Number(String(s).replace(/\D/g, '')) || 0;
            },


            // =========================
            // LABEL
            // =========================

            labelKategori(c) {

                const map = {
                    oli: 'Oli',
                    ban: 'Ban & Velg',
                    rem: 'Kampas Rem',
                    busi: 'Busi',
                    filter: 'Filter',
                    lainnya: 'Lainnya'
                };

                return map[c] || c;
            },


            labelStatus(s) {

                return {
                    tersedia: 'Stok Tersedia',
                    menipis: 'Stok Menipis',
                    habis: 'Stok Habis'
                }[s] || s;

            },


            badge(s) {

                return {
                    tersedia: 'bg-green-50 text-green-600',
                    menipis: 'bg-amber-50 text-amber-600',
                    habis: 'bg-red-50 text-red-600'
                }[s] || 'bg-gray-50 text-gray-600';

            },


            emojiByCat(c) {

                return {
                    oli: '🛢️',
                    ban: '🛞',
                    rem: '🛑',
                    busi: '⚡',
                    filter: '🌀',
                    lainnya: '📦'
                }[c] || '📦';

            },


            statusByStock(stock) {

                if (stock <= 0) return 'habis';
                if (stock <= 8) return 'menipis';

                return 'tersedia';
            },


            // =========================
            // STATISTIK
            // =========================

            countStatus(s) {

                return this.rows.filter(
                    p => p.status === s
                ).length;

            },


            nilaiStok() {

                return this.rows.reduce(
                    (t, p) => t + p.price * p.stock,
                    0
                );

            },


            // =========================
            // FILTER
            // =========================

            kategoriList() {

                return [
                    ...new Set(
                        this.rows.map(
                            p => this.labelKategori(p.cat)
                        )
                    )
                ];

            },


            filtered() {

                const q = this.query.trim().toLowerCase();

                return this.rows.filter(p => {

                    const okQ =
                        !q ||
                        p.name.toLowerCase().includes(q) ||
                        p.sku.toLowerCase().includes(q);

                    const okKat =
                        this.kategori === 'semua' ||
                        this.labelKategori(p.cat) === this.kategori;

                    const okStatus =
                        this.statusFilter === 'semua' ||
                        p.status === this.statusFilter;

                    return okQ && okKat && okStatus;

                });

            },


            // =========================
            // IMAGE
            // =========================

            pickImage(e) {

                const file = e.target.files[0];

                if (!file) return;

                const reader = new FileReader();

                reader.onload = (ev) => {
                    this.form.image = ev.target.result;
                };

                reader.readAsDataURL(file);

            },


            // =========================
            // TAMBAH PRODUK
            // =========================

            openAdd() {

                this.mode = 'add';

                this.error = '';

                this.editId = null;

                this.form = {
                    sku: '',
                    name: '',
                    cat: 'oli',
                    stock: 0,
                    price: 0,
                    image: ''
                };

                this.showForm = true;

            },


            // =========================
            // UPDATE PRODUK
            // =========================

            openEdit(p) {

                this.mode = 'edit';

                this.error = '';

                this.editId = p.id;

                this.form = {
                    sku: p.sku,
                    name: p.name,
                    cat: p.cat,
                    stock: p.stock,
                    price: p.price,
                    image: p.image || ''
                };

                this.showForm = true;

            },


            // =========================
            // BUKA MODAL BELI
            // =========================

            openBeli(p) {

                this.beliError = '';

                this.beli = {
                    id: p.id,
                    name: p.name,
                    stock: Number(p.stock) || 0,
                    qty: 1,
                    buy_price: 0,
                    supplier: ''
                };

                this.showBeli = true;

            },


            // =========================
            // SIMPAN PRODUK
            // =========================

            async saveProduk() {

                if (
                    !this.form.name.trim() ||
                    !this.form.sku.trim()
                ) {

                    this.error =
                        'Kode dan Nama produk wajib diisi.';

                    return;
                }

                if (this.saving) return;

                this.saving = true;

                try {

                    const res = await fetch(
                        '/produk/simpan',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json'
                            },

                            body: JSON.stringify({

                                id: this.editId,

                                sku: this.form.sku,

                                name: this.form.name,

                                cat: this.form.cat,

                                stock:
                                    Number(this.form.stock) || 0,

                                price:
                                    Number(this.form.price) || 0,

                                image:
                                    this.form.image || ''

                            })

                        }
                    );

                    const data = await res.json();

                    if (!res.ok) {

                        this.error =
                            data.message ||
                            'Gagal menyimpan produk.';

                        this.saving = false;

                        return;
                    }

                    window.location.reload();

                } catch (e) {

                    this.error =
                        'Kesalahan koneksi ke server.';

                    this.saving = false;

                }

            },


            // =========================
            // DELETE
            // =========================

                        openBeli(p) {
                this.beliError = '';
                this.beli = { id: p.id, name: p.name, stock: p.stock, qty: 1, buy_price: Math.round(p.price * 0.7), supplier: '' };
                this.showBeli = true;
            },
            async simpanBeli() {
                if (this.beli.qty < 1) { this.beliError = 'Jumlah minimal 1.'; return; }
                if (this.saving) return;
                this.saving = true;
                try {
                    const res = await fetch('/produk/beli', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            product_id: this.beli.id,
                            qty: Number(this.beli.qty) || 0,
                            buy_price: Number(this.beli.buy_price) || 0,
                            supplier: this.beli.supplier || '',
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) { this.beliError = data.message || 'Gagal menyimpan pembelian.'; this.saving = false; return; }
                    window.location.reload();
                } catch (e) {
                    this.beliError = 'Kesalahan koneksi ke server.';
                    this.saving = false;
                }
            },

            askDelete(p) { this.target = p; this.showDelete = true; },


            async confirmDelete() {

                if (this.saving) return;

                this.saving = true;

                try {

                    const res = await fetch(
                        '/produk/hapus',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json'
                            },

                            body: JSON.stringify({
                                id: this.target.id
                            })

                        }
                    );

                    if (!res.ok) {

                        alert('Gagal menghapus produk.');

                        this.saving = false;

                        return;
                    }

                    window.location.reload();

                } catch (e) {

                    alert('Kesalahan koneksi ke server.');

                    this.saving = false;

                }

            },


            // =========================
            // SIMPAN BELI / RESTOCK
            // =========================

                       async simpanBeli() {
                if (this.beli.qty < 1) { this.beliError = 'Jumlah minimal 1.'; return; }
                if (this.saving) return;
                this.saving = true;
                this.beliError = '';
                try {
                    const res = await fetch('/produk/beli', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            product_id: this.beli.id,
                            qty: Number(this.beli.qty) || 0,
                            buy_price: Number(this.beli.buy_price) || 0,
                            supplier: this.beli.supplier || '',
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) { this.beliError = data.message || 'Gagal menyimpan pembelian.'; this.saving = false; return; }
                    window.location.reload();
                } catch (e) {
                    this.beliError = 'Kesalahan koneksi ke server.';
                    this.saving = false;
                }
            },

        }));

    });

</script>

@endpush