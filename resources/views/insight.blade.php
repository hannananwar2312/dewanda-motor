@extends('layouts.app')

@section('title', 'AI Insight · Dewanda Motor')

@php($isAdmin = session('role') === 'admin')

@section('content')

<div x-data="insight" class="flex h-screen overflow-hidden bg-gray-50">

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
                @if ($isAdmin)
                <a href="/produk" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="boxes" class="h-5 w-5"></i> Produk & Stok
                </a>
                @endif
                <a href="/insight" class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
                    <i data-lucide="sparkles" class="h-5 w-5"></i> AI Insight
                </a>
                @if ($isAdmin)
                <a href="/pembukuan" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="book-open" class="h-5 w-5"></i> Pembukuan
                </a>
                @endif
            </nav>
        </div>

        <div class="flex items-center gap-3 px-2 py-2">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-800 text-sm font-bold text-white">
                {{ strtoupper(substr(session('user'), 0, 1)) }}
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
                <h2 class="text-lg font-bold">AI Insight</h2>
                <p class="hidden text-xs text-gray-400 sm:block">
                    {{ $isAdmin ? 'Ringkasan toko & rekomendasi bisnis dari AI.' : 'Tips jualan & promo dari AI buat bantu kamu melayani pembeli.' }}
                </p>
            </div>
        </header>

        <div class="p-4 lg:p-6">

            <div class="rounded-2xl border border-gray-200 bg-white p-5">
                <div class="mb-2 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="h-5 w-5 text-gray-500"></i>
                    <h3 class="font-bold">{{ $isAdmin ? 'Ringkasan Toko' : 'Info Hari Ini' }}</h3>
                </div>
                <p class="text-sm leading-relaxed text-gray-600">{{ $ringkasan }}</p>
            </div>

            {{-- Statistik: admin lihat semua, kasir hanya info produk & penjualan --}}
            <div class="mt-4 grid grid-cols-2 gap-3 {{ $isAdmin ? 'lg:grid-cols-4' : '' }}">
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Total Produk</p>
                    <p class="mt-1 text-xl font-bold">{{ $totalProduk }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Unit Terjual</p>
                    <p class="mt-1 text-xl font-bold">{{ number_format($totalTerjual, 0, ',', '.') }}</p>
                </div>
                @if ($isAdmin)
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Estimasi Omzet</p>
                    <p class="mt-1 text-xl font-bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Nilai Stok</p>
                    <p class="mt-1 text-xl font-bold">Rp {{ number_format($nilaiStok, 0, ',', '.') }}</p>
                </div>
                @endif
            </div>

            {{-- Kartu AI --}}
            <div class="mt-4 rounded-2xl border border-red-100 bg-gradient-to-br from-red-50 to-white p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-600">
                            <i data-lucide="sparkles" class="h-5 w-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold">{{ $isAdmin ? 'Rekomendasi AI' : 'Tips Jualan AI' }}</h3>
                            <p class="text-xs text-gray-400">
                                <span x-show="aiTime" x-text="'Diperbarui ' + aiTime"></span>
                                <span x-show="!aiTime">Belum ada analisis. Klik Generate.</span>
                            </p>
                        </div>
                    </div>
                    <button x-on:click="generate()" :disabled="loading"
                        class="flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:bg-gray-300">
                        <i data-lucide="sparkles" class="h-4 w-4" x-show="!loading"></i>
                        <svg x-show="loading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span x-text="loading ? 'Menganalisis…' : (aiText ? 'Generate Ulang' : '{{ $isAdmin ? 'Generate Analisis' : 'Minta Tips Jualan' }}')"></span>
                    </button>
                </div>

                <div x-show="error" x-cloak class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600" x-text="error"></div>

                <div x-show="aiText" x-cloak class="mt-4 rounded-xl bg-white p-4 text-sm leading-relaxed text-gray-700">
                    <div x-html="renderText(aiText)"></div>
                </div>

                <div x-show="!aiText && !loading && !error" class="mt-4 rounded-xl border border-dashed border-red-200 bg-white/60 px-4 py-8 text-center">
                    <i data-lucide="bot" class="mx-auto mb-2 h-7 w-7 text-red-300"></i>
                    <p class="text-sm text-gray-500">
                        @if ($isAdmin)
                            Klik <span class="font-semibold">Generate Analisis</span> untuk minta rekomendasi bisnis dari AI berdasarkan data toko.
                        @else
                            Klik <span class="font-semibold">Minta Tips Jualan</span> buat dapet ide produk yang bisa ditawarkan & dipromoin ke pembeli.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Produk Terlaris (semua role) + Stok Menipis (admin saja) --}}
            <div class="mt-4 grid grid-cols-1 gap-4 {{ $isAdmin ? 'lg:grid-cols-2' : '' }}">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-3 flex items-center gap-2">
                        <i data-lucide="trending-up" class="h-5 w-5 text-green-500"></i>
                        <h3 class="font-bold">{{ $isAdmin ? 'Produk Terlaris' : 'Produk Laris — Tawarkan ke Pembeli' }}</h3>
                    </div>
                    @forelse ($terlaris as $p)
                        <div class="flex items-center justify-between border-b border-gray-50 py-2.5 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-lg {{ $p['tint'] }}">{{ $p['emoji'] }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $p['name'] }}</p>
                                    <p class="text-xs text-gray-400">Stok {{ $p['stock'] }}</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-600">{{ $p['sold'] }} terjual</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-400">Belum ada penjualan.</p>
                    @endforelse
                </div>

                @if ($isAdmin)
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-3 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-amber-500"></i>
                        <h3 class="font-bold">Stok Menipis</h3>
                    </div>
                    @forelse ($menipis as $p)
                        <div class="flex items-center justify-between border-b border-gray-50 py-2.5 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl text-lg {{ $p['tint'] }}">{{ $p['emoji'] }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $p['name'] }}</p>
                                    <p class="text-xs text-gray-400">SKU {{ $p['sku'] }}</p>
                                </div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $p['stock'] <= 0 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600' }}">sisa {{ $p['stock'] }}</span>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-400">Semua stok aman.</p>
                    @endforelse
                </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('insight', () => ({
            sidebarOpen: false,
            loading: false,
            error: '',
            aiText: @js($aiText ?? ''),
            aiTime: @js($aiTime ?? ''),
            csrf: '{{ csrf_token() }}',

            async generate() {
                this.loading = true;
                this.error = '';
                try {
                    const res = await fetch('/insight/ai', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                    });
                    const data = await res.json();
                    if (data.ok) {
                        this.aiText = data.text;
                        this.aiTime = data.time;
                    } else {
                        this.error = data.error || 'Gagal memanggil AI. Coba lagi.';
                    }
                } catch (e) {
                    this.error = 'Terjadi kesalahan koneksi ke AI.';
                }
                this.loading = false;
            },

            renderText(t) {
                if (!t) return '';
                let html = t
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.+?)\*/g, '<em>$1</em>');
                return html
                    .split(/\n{2,}/)
                    .map(p => '<p class="mb-3 last:mb-0">' + p.replace(/\n/g, '<br>') + '</p>')
                    .join('');
            },
        }));
    });
</script>
@endpush