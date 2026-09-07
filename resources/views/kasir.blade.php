@extends('layouts.app')

@section('title', 'Kasir · Dewanda Motor')

@php
$cats = [
    ['id' => 'semua',  'label' => 'Semua',      'icon' => 'layout-grid'],
    ['id' => 'oli',    'label' => 'Oli',        'icon' => 'droplet'],
    ['id' => 'ban',    'label' => 'Ban & Velg', 'icon' => 'circle-dot'],
    ['id' => 'rem',    'label' => 'Kampas Rem', 'icon' => 'gauge'],
    ['id' => 'busi',   'label' => 'Busi',       'icon' => 'zap'],
    ['id' => 'filter', 'label' => 'Filter',     'icon' => 'filter'],
];
@endphp

@section('content')

<style>
    @media print {
        body * { visibility: hidden; }
        #struk-cetak, #struk-cetak * { visibility: visible; }
        #struk-cetak {
            position: absolute; left: 0; top: 0; width: 80mm;
            padding: 6mm 5mm; font-family: 'Courier New', monospace; color: #000;
        }
        .no-print { display: none !important; }
        @page { margin: 0; }
    }
</style>

<div x-data="pos" class="flex h-screen flex-col overflow-hidden bg-gray-50">

    <header class="flex h-16 shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4">
        <div class="flex w-52 items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-600">
                <i data-lucide="bike" class="h-5 w-5 text-white"></i>
            </div>
            <span class="text-lg font-bold">Dewanda Motor</span>
        </div>
        <button class="hidden text-gray-400 hover:text-gray-600 md:block">
            <i data-lucide="panel-left" class="h-5 w-5"></i>
        </button>
        <div class="relative mx-auto w-full max-w-2xl">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
            <input x-model="query" placeholder="Cari produk atau SKU…"
                class="w-full rounded-full border border-gray-200 bg-white py-2.5 pl-11 pr-11 text-sm outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100">
            <i data-lucide="sliders-horizontal" class="absolute right-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <aside class="flex w-52 shrink-0 flex-col justify-between border-r border-gray-200 bg-white p-3">
            <nav class="space-y-1">
                <a href="/kasir" class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
                    <i data-lucide="shopping-cart" class="h-5 w-5"></i> Kasir
                </a>
                <a href="/produk" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="boxes" class="h-5 w-5"></i> Produk & Stok
                </a>
                <a href="/insight" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="sparkles" class="h-5 w-5"></i> AI Insight
                </a>
                <a href="/pembukuan" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="book-open" class="h-5 w-5"></i> Pembukuan
                </a>
            </nav>

            <div class="flex items-center gap-3 px-2 py-2">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-800 text-sm font-bold text-white">
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

        <main class="relative flex-1 overflow-y-auto">
            <div class="p-6 pb-28">
                <div class="mb-5 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="h-6 w-6"></i>
                    <h2 class="text-xl font-bold">Produk</h2>
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                    <template x-for="p in filtered()" :key="p.id">
                        <div class="flex flex-col rounded-2xl border border-gray-200 bg-white p-3 transition hover:shadow-md">
                            <div class="relative mb-3 flex h-28 items-center justify-center rounded-xl bg-gray-50">
                                <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-2xl" :class="p.tint">
                                    <span x-text="p.emoji"></span>
                                </div>
                                <span class="absolute bottom-2 right-2 rounded-md px-2 py-0.5 text-xs font-semibold"
                                    :class="p.stock <= 5 ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-500'">
                                    Stok : <span x-text="p.stock"></span>
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold leading-tight text-gray-900" x-text="p.name"></h3>
                            <p class="mt-0.5 text-xs text-gray-400">SKU : <span x-text="p.sku"></span></p>
                            <p class="mt-2 text-base font-bold text-gray-900" x-text="rupiah(p.price)"></p>
                            <button x-on:click="add(p)"
                                class="mt-3 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.99]">
                                Tambah Pesanan
                            </button>
                        </div>
                    </template>
                </div>

                <div x-show="filtered().length === 0"
                    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 py-20 text-center">
                    <i data-lucide="search" class="mb-3 h-8 w-8 text-gray-300"></i>
                    <p class="text-sm text-gray-500">Produk tidak ditemukan.</p>
                    <p class="text-xs text-gray-400">Coba kata kunci atau kategori lain.</p>
                </div>
            </div>

            <div class="pointer-events-none sticky bottom-4 flex justify-center">
                <div class="pointer-events-auto flex gap-3 rounded-2xl bg-white/90 p-3 shadow-lg ring-1 ring-gray-200 backdrop-blur">
                    @foreach ($cats as $c)
                        <button x-on:click="category = '{{ $c['id'] }}'"
                            class="flex h-20 w-20 flex-col items-center justify-center gap-1.5 rounded-xl border text-xs font-semibold transition"
                            :class="category === '{{ $c['id'] }}' ? 'border-red-600 bg-red-600 text-white' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'">
                            <i data-lucide="{{ $c['icon'] }}" class="h-5 w-5"></i>
                            <span class="leading-tight text-center">{{ $c['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </main>

        <aside class="flex w-80 shrink-0 flex-col border-l border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-100 p-5">
                <div>
                    <h3 class="text-lg font-bold">Detail order</h3>
                    <p class="text-xs text-gray-400">#<span x-text="invoice"></span></p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400">
                    <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-5">
                <div x-show="cart.length > 0" class="space-y-4">
                    <template x-for="i in cart" :key="i.id">
                        <div class="border-b border-gray-100 pb-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold" x-text="i.name"></p>
                                    <p class="text-xs text-gray-400">SKU : <span x-text="i.sku"></span></p>
                                </div>
                                <button x-on:click="remove(i.id)" class="text-gray-300 hover:text-red-500">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/></svg>
                                </button>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-sm font-bold" x-text="rupiah(i.price * i.qty)"></p>
                                <div class="flex items-center gap-3 rounded-lg border border-gray-200 px-2 py-1">
                                    <button x-on:click="changeQty(i.id, -1)" class="text-red-500 hover:text-red-600 text-lg leading-none">&minus;</button>
                                    <span class="w-5 text-center text-sm font-semibold" x-text="i.qty"></span>
                                    <button x-on:click="changeQty(i.id, 1)" class="text-red-500 hover:text-red-600 text-lg leading-none">+</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="cart.length === 0" class="flex h-full flex-col items-center justify-center text-center">
                    <i data-lucide="shopping-cart" class="mb-3 h-9 w-9 text-gray-200"></i>
                    <p class="text-sm font-medium text-gray-500">Belum ada pesanan</p>
                    <p class="text-xs text-gray-400">Pilih produk untuk memulai transaksi.</p>
                </div>
            </div>

            <div class="border-t border-gray-100 p-5">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500"><span>SubTotal</span><span class="font-medium text-gray-700" x-text="rupiah(subtotal)"></span></div>
                    <div class="flex justify-between text-gray-500"><span>Diskon</span><span class="font-medium text-gray-700" x-text="rupiah(diskon)"></span></div>
                    <div class="flex justify-between text-gray-500"><span>Pajak (PPN 11%)</span><span class="font-medium text-gray-700" x-text="rupiah(pajak)"></span></div>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3">
                    <span class="text-sm font-semibold">Total Pembayaran</span>
                    <span class="text-lg font-bold text-red-600" x-text="rupiah(total)"></span>
                </div>

                <div class="mt-4">
                    <p class="mb-2 text-xs font-medium text-gray-500">Metode Pembayaran</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button x-on:click="method = 'tunai'"
                            :class="method === 'tunai' ? 'border-red-600 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                            class="flex items-center justify-center gap-2 rounded-xl border py-2.5 text-sm font-semibold transition">
                            <i data-lucide="banknote" class="h-4 w-4"></i> Tunai
                        </button>
                        <button x-on:click="method = 'qris'"
                            :class="method === 'qris' ? 'border-red-600 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                            class="flex items-center justify-center gap-2 rounded-xl border py-2.5 text-sm font-semibold transition">
                            <i data-lucide="qr-code" class="h-4 w-4"></i> QRIS
                        </button>
                    </div>

                    <div x-show="method === 'tunai'" x-cloak class="mt-3">
                        <p class="mb-1 text-xs font-medium text-gray-500">Uang Diterima</p>
                        <input type="text" inputmode="numeric" :value="formatRibuan(cashInput)"
                            x-on:input="cashInput = angkaSaja($event.target.value)" placeholder="0"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                        <div class="mt-2 flex flex-wrap gap-2">
                            <template x-for="n in quickCash()" :key="n">
                                <button x-on:click="cashInput = n"
                                    class="rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 hover:border-red-300 hover:text-red-600"
                                    x-text="rupiah(n)"></button>
                            </template>
                            <button x-on:click="cashInput = total"
                                class="rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 hover:border-red-300 hover:text-red-600">Uang Pas</button>
                        </div>
                        <div class="mt-2 flex justify-between text-sm">
                            <span class="text-gray-500">Kembalian</span>
                            <span class="font-semibold" :class="kembalian() < 0 ? 'text-red-600' : 'text-green-600'" x-text="rupiah(kembalian())"></span>
                        </div>
                    </div>
                </div>

                    <button x-on:click="checkout()" :disabled="!bisaBayar() || saving"
                    class="mt-4 w-full rounded-xl bg-red-600 py-3 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.99] disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400">
                    Selesai & Cetak Struk
                </button>
            </div>
        </aside>
    </div>

    <div x-show="showReceipt" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-start justify-between no-print">
                <div>
                    <h3 class="text-lg font-bold">Struk Pembayaran</h3>
                    <p class="text-xs text-gray-400">Transaksi berhasil</p>
                </div>
                <button x-on:click="newTrx()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div id="struk-cetak" class="rounded-xl border border-dashed border-gray-200 p-4">
                <div class="mb-3 text-center">
                    <p class="text-base font-bold">Dewanda Motor</p>
                    <p class="text-[11px] text-gray-500">Jl.Tipar, Kec. rawalo, Kabupaten Banyumas, Purwokerto</p>
                    <p class="text-[11px] text-gray-500">Telp. 812-2550-0632</p>
                    <div class="my-2 border-t border-dashed border-gray-300"></div>
                    <p class="text-[11px] text-gray-500">No : #<span x-text="invoice"></span></p>
                    <p class="text-[11px] text-gray-500" x-text="'Tgl : ' + tanggal"></p>
                    <p class="text-[11px] text-gray-500">Kasir : {{ $user }}</p>
                </div>

                <div class="border-y border-dashed border-gray-300 py-2 text-sm">
                    <template x-for="i in cart" :key="i.id">
                        <div class="mb-1">
                            <p class="text-[13px] font-medium" x-text="i.name"></p>
                            <div class="flex justify-between text-[12px] text-gray-600">
                                <span x-text="i.qty + ' x ' + rupiah(i.price)"></span>
                                <span class="font-medium" x-text="rupiah(i.price * i.qty)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="space-y-1 py-2 text-[12px]">
                    <div class="flex justify-between text-gray-600"><span>SubTotal</span><span x-text="rupiah(subtotal)"></span></div>
                    <div class="flex justify-between text-gray-600"><span>Diskon</span><span x-text="rupiah(diskon)"></span></div>
                    <div class="flex justify-between text-gray-600"><span>Pajak (PPN 11%)</span><span x-text="rupiah(pajak)"></span></div>
                    <div class="flex justify-between border-t border-dashed border-gray-300 pt-1 text-sm font-bold"><span>TOTAL</span><span x-text="rupiah(total)"></span></div>
                </div>

                <div class="space-y-1 border-t border-dashed border-gray-300 py-2 text-[12px]">
                    <div class="flex justify-between text-gray-600">
                        <span>Metode</span>
                        <span class="font-semibold uppercase" x-text="method"></span>
                    </div>
                    <template x-if="method === 'tunai'">
                        <div>
                            <div class="flex justify-between text-gray-600"><span>Tunai</span><span x-text="rupiah(cashInput)"></span></div>
                            <div class="flex justify-between text-gray-600"><span>Kembalian</span><span x-text="rupiah(kembalian())"></span></div>
                        </div>
                    </template>
                </div>

                <p class="mt-2 text-center text-[12px] text-gray-500">~ Terima kasih telah berbelanja ~</p>
                <p class="text-center text-[11px] text-gray-400">Barang yang sudah dibeli tidak dapat dikembalikan</p>
            </div>

            <div class="mt-4 flex gap-3 no-print">
                <button x-on:click="window.print()"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i data-lucide="printer" class="h-4 w-4"></i> Cetak
                </button>
                <button x-on:click="newTrx()"
                    class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pos', () => ({
            products: @json($products),
            cart: [],
            category: 'semua',
            query: '',
            showReceipt: false,
            method: 'tunai',
            cashInput: 0,
            saving: false,
            invoice: '{{ $invoice }}',
            csrf: '{{ csrf_token() }}',
            tanggal: new Date().toLocaleString('id-ID'),

            filtered() {
                const q = this.query.trim().toLowerCase();
                return this.products.filter(p =>
                    (this.category === 'semua' || p.cat === this.category) &&
                    (!q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q))
                );
            },
            add(p) {
                const f = this.cart.find(i => i.id === p.id);
                f ? f.qty++ : this.cart.push({ ...p, qty: 1 });
            },
            changeQty(id, d) {
                const i = this.cart.find(x => x.id === id);
                if (!i) return;
                i.qty += d;
                if (i.qty <= 0) this.remove(id);
            },
            remove(id) {
                this.cart = this.cart.filter(i => i.id !== id);
            },
            get subtotal() { return this.cart.reduce((s, i) => s + i.price * i.qty, 0); },
            get diskon()   { return 0; },
            get pajak()    { return Math.round((this.subtotal - this.diskon) * 0.11); },
            get total()    { return this.subtotal - this.diskon + this.pajak; },
            rupiah(n)      { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            formatRibuan(n){ return Number(n || 0).toLocaleString('id-ID'); },
            angkaSaja(s)   { return Number(String(s).replace(/\D/g, '')) || 0; },

            kembalian()    { return (Number(this.cashInput) || 0) - this.total; },
            quickCash() {
                const t = this.total;
                const a = Math.ceil(t / 50000) * 50000;
                const b = Math.ceil(t / 100000) * 100000;
                return [...new Set([a, b])].filter(n => n > t);
            },
            bisaBayar() {
                if (this.cart.length === 0) return false;
                if (this.method === 'tunai') return (Number(this.cashInput) || 0) >= this.total;
                return true;
            },
                        async checkout() {
                if (!this.bisaBayar() || this.saving) return;
                this.saving = true;
                try {
                    const res = await fetch('/kasir/simpan', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            payment_method: this.method === 'tunai' ? 'cash' : 'qris',
                            cash_received: this.method === 'tunai' ? this.cashInput : null,
                            items: this.cart.map(i => ({ id: i.id, qty: i.qty })),
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        alert(data.message || 'Gagal menyimpan transaksi.');
                        this.saving = false;
                        return;
                    }
                    this.invoice = data.invoice;
                    this.tanggal = data.tanggal;
                    this.showReceipt = true;
                } catch (e) {
                    alert('Terjadi kesalahan koneksi ke server.');
                }
                this.saving = false;
            },
            newTrx() {
                window.location.reload();
            },
        }));
    });
</script>
@endpush