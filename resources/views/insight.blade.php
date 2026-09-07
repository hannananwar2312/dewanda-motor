@extends('layouts.app')

@section('title', 'AI Insight · Dewanda Motor')

@php
    $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

    // Mencegah DivisionByZeroError jika belum ada produk terjual
    $maxSold = max(1, ...array_map(fn ($p) => $p['sold'], $terlaris ?: []));
@endphp

@section('content')
<div class="flex h-screen flex-col overflow-hidden bg-gray-50">

    <header class="flex h-16 shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4">
        <div class="flex w-52 items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-600">
                <i data-lucide="bike" class="h-5 w-5 text-white"></i>
            </div>
            <span class="text-lg font-bold">Dewanda Motor</span>
        </div>

        <div class="mx-auto flex items-center gap-2 text-sm font-semibold text-gray-500">
            <i data-lucide="sparkles" class="h-4 w-4 text-red-600"></i>
            AI Insight
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
                   class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="boxes" class="h-5 w-5"></i>
                    Produk & Stok
                </a>

                <a href="/insight"
                   class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
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

                <div class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-800 text-sm font-bold text-white">
                    {{ strtoupper(substr($user ?? 'K', 0, 1)) }}
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

            <div class="mb-5 flex items-center gap-2">
                <i data-lucide="sparkles" class="h-6 w-6 text-red-600"></i>
                <h2 class="text-xl font-bold">AI Insight</h2>
            </div>

            {{-- Ringkasan --}}
            <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-5">

                <div class="mb-2 flex items-center gap-2">
                    <i data-lucide="sparkles" class="h-5 w-5 text-red-600"></i>
                    <h3 class="font-bold text-gray-900">Ringkasan</h3>
                </div>

                <p class="text-sm leading-relaxed text-gray-700">
                    {{ $ringkasan }}
                </p>

            </div>

            {{-- Statistik --}}
            <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

                {{-- Total Produk --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">
                        Total Produk
                    </p>

                    <p class="mt-1 text-2xl font-bold">
                        {{ $totalProduk }}
                    </p>
                </div>

                {{-- Unit Terjual --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">
                        Unit Terjual
                    </p>

                    <p class="mt-1 text-2xl font-bold">
                        {{ number_format($totalTerjual, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Estimasi Omzet --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">
                        Estimasi Omzet
                    </p>

                    <p class="mt-1 text-xl font-bold text-red-600">
                        {{ $rp($totalOmzet) }}
                    </p>
                </div>

                {{-- Nilai Stok --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">
                        Nilai Stok
                    </p>

                    <p class="mt-1 text-xl font-bold">
                        {{ $rp($nilaiStok) }}
                    </p>
                </div>

            </div>

            {{-- Produk Terlaris & Stok Menipis --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- Produk Terlaris --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5">

                    <div class="mb-4 flex items-center gap-2">
                        <i data-lucide="trending-up" class="h-5 w-5 text-red-600"></i>
                        <h3 class="font-bold">
                            Produk Terlaris
                        </h3>
                    </div>

                    <div class="space-y-4">

                        @forelse ($terlaris as $p)

                            <div>

                                <div class="mb-1 flex justify-between text-sm">

                                    <span class="font-medium">
                                        {{ $p['emoji'] }} {{ $p['name'] }}
                                    </span>

                                    <span class="text-gray-500">
                                        {{ $p['sold'] }} unit
                                    </span>

                                </div>

                                <div class="h-2 w-full rounded-full bg-gray-100">

                                    <div
                                        class="h-2 rounded-full bg-red-600"
                                        style="width: {{ round(($p['sold'] / $maxSold) * 100) }}%">
                                    </div>

                                </div>

                            </div>

                        @empty

                            <p class="text-sm text-gray-500">
                                Belum ada data penjualan.
                            </p>

                        @endforelse

                    </div>

                </div>

                {{-- Stok Menipis --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5">

                    <div class="mb-4 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-red-600"></i>

                        <h3 class="font-bold">
                            Stok Menipis
                        </h3>
                    </div>

                    <div class="space-y-3">

                        @forelse ($menipis as $p)

                            <div class="flex items-center justify-between rounded-xl border border-gray-100 p-3">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl text-xl {{ $p['tint'] }}">
                                        {{ $p['emoji'] }}
                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold">
                                            {{ $p['name'] }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            SKU : {{ $p['sku'] }}
                                        </p>

                                    </div>

                                </div>

                                <span class="rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-600">
                                    Sisa {{ $p['stock'] }}
                                </span>

                            </div>

                        @empty

                            <p class="text-sm text-gray-500">
                                Semua stok aman 🎉
                            </p>

                        @endforelse

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>
@endsection