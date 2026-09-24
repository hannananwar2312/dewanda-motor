@extends('layouts.app')

@section('title', 'Keuangan · Dewanda Motor')

@section('content')
<div x-data="keuangan()" class="flex h-screen flex-col overflow-hidden bg-gray-50">

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
                <a href="/kasir" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
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
                <a href="/keuangan" class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
                    <i data-lucide="wallet" class="h-5 w-5"></i> Keuangan
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

        <main class="flex-1 overflow-y-auto p-6">
            <div class="mb-5">
                <h2 class="flex items-center gap-2 text-xl font-bold"><i data-lucide="wallet" class="h-6 w-6"></i> Modal</h2>
                <p class="text-sm text-gray-400">Kelola modal awal dan penambahan modal</p>
            </div>

            {{-- Kartu ringkasan --}}
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <p class="text-sm text-gray-500">Modal Awal</p>
                    <p class="mt-1 text-2xl font-bold" x-text="rupiah(modalAwal)"></p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <p class="text-sm text-gray-500">Total Tambahan Modal</p>
                    <p class="mt-1 text-2xl font-bold" x-text="rupiah(totalTambahan)"></p>
                </div>
                <div class="rounded-2xl border border-red-100 bg-red-50 p-5">
                    <p class="text-sm text-red-500">Total Modal</p>
                    <p class="mt-1 text-2xl font-bold text-red-600" x-text="rupiah(totalModal)"></p>
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="mb-6 flex flex-wrap gap-3">
                <button x-show="!punyaAwal" x-on:click="openForm('awal')"
                    class="flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                    <i data-lucide="plus" class="h-4 w-4"></i> Isi Modal Awal
                </button>
                <button x-on:click="openForm('tambahan')"
                    class="flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                    <i data-lucide="plus" class="h-4 w-4"></i> Tambah Modal
                </button>
            </div>

            {{-- Riwayat tambahan modal --}}
            <div class="rounded-2xl border border-gray-200 bg-white">
                <div class="p-5"><h3 class="font-bold">Riwayat Penambahan Modal</h3></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">No</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">Nominal</th>
                                <th class="px-4 py-3 font-medium">Keterangan</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(r, idx) in tambahan" :key="r.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500" x-text="idx + 1"></td>
                                    <td class="px-4 py-3" x-text="fmtTgl(r.date)"></td>
                                    <td class="px-4 py-3 font-semibold" x-text="rupiah(r.amount)"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="r.note || '-'"></td>
                                    <td class="px-4 py-3">
                                        <button x-on:click="hapus(r.id)" class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="tambahan.length === 0">
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">Belum ada penambahan modal.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    {{-- Modal form --}}
    <div x-show="showForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold" x-text="form.type === 'awal' ? 'Isi Modal Awal' : 'Tambah Modal'"></h3>
                <button x-on:click="showForm = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="h-5 w-5"></i></button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Nominal (Rp)</label>
                    <input type="text" inputmode="numeric" :value="formatRibuan(form.amount)" x-on:input="form.amount = angkaSaja($event.target.value)" placeholder="0"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Tanggal</label>
                    <input type="date" x-model="form.date"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Keterangan</label>
                    <input x-model="form.note" placeholder="mis. tambahan modal dari pemilik"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                </div>
                <p x-show="error" x-cloak class="text-sm text-red-600" x-text="error"></p>
            </div>
            <div class="mt-5 flex gap-3">
                <button x-on:click="showForm = false" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button x-on:click="simpan()" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('keuangan', () => ({
            modalAwal: {{ $modalAwal }},
            punyaAwal: {{ $punyaAwal ? 'true' : 'false' }},
            totalTambahan: {{ $totalTambahan }},
            totalModal: {{ $totalModal }},
            tambahan: @json($tambahan),
            showForm: false,
            error: '',
            csrf: '{{ csrf_token() }}',
            form: { type: 'tambahan', amount: 0, date: '{{ date('Y-m-d') }}', note: '' },

            rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            formatRibuan(n) { return Number(n || 0).toLocaleString('id-ID'); },
            angkaSaja(s) { return Number(String(s).replace(/\D/g, '')) || 0; },
            fmtTgl(iso) { return new Date(iso).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }); },

            openForm(type) {
                this.error = '';
                this.form = { type: type, amount: 0, date: '{{ date('Y-m-d') }}', note: '' };
                this.showForm = true;
            },
            async simpan() {
                if (this.form.amount < 1) { this.error = 'Nominal wajib diisi.'; return; }
                try {
                    const res = await fetch('/keuangan/modal', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json();
                    if (!res.ok) { this.error = data.message || 'Gagal menyimpan.'; return; }
                    window.location.reload();
                } catch (e) { this.error = 'Kesalahan koneksi.'; }
            },
            async hapus(id) {
                if (!confirm('Hapus penambahan modal ini?')) return;
                try {
                    const res = await fetch('/keuangan/modal/hapus', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ id }),
                    });
                    if (!res.ok) { alert('Gagal menghapus.'); return; }
                    window.location.reload();
                } catch (e) { alert('Kesalahan koneksi.'); }
            },
        }));
    });
</script>
@endpush