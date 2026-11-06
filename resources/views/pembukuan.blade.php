@extends('layouts.app')

@section('title', 'Pembukuan · Dewanda Motor')

@section('content')

<div x-data="pembukuan" class="flex h-screen overflow-hidden bg-gray-50">

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
                <a href="/produk" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="boxes" class="h-5 w-5"></i> Produk & Stok
                </a>
                @endif
                <a href="/insight" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">
                    <i data-lucide="sparkles" class="h-5 w-5"></i> AI Insight
                </a>
                @if (session('role') === 'admin')
                <a href="/pembukuan" class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
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
                <h2 class="text-lg font-bold">Pembukuan</h2>
                <p class="hidden text-xs text-gray-400 sm:block">Riwayat penjualan, pembelian, dan modal.</p>
            </div>
        </header>

        <div class="p-4 lg:p-6">

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Total Penjualan</p>
                    <p class="mt-1 text-lg font-bold text-green-600">Rp {{ number_format(collect($rows)->sum('total'), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Total Pembelian</p>
                    <p class="mt-1 text-lg font-bold text-red-600">Rp {{ number_format(collect($pembelian)->sum('total'), 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Total Modal</p>
                    <p class="mt-1 text-lg font-bold">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4">
                    <p class="text-xs text-gray-400">Laba Kotor (est.)</p>
                    <p class="mt-1 text-lg font-bold">Rp {{ number_format(collect($rows)->sum('total') - collect($pembelian)->sum('total'), 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-5 flex gap-1 rounded-xl border border-gray-200 bg-white p-1">
                <button x-on:click="tab = 'jual'" :class="tab === 'jual' ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition">Riwayat Penjualan</button>
                <button x-on:click="tab = 'beli'" :class="tab === 'beli' ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition">Riwayat Pembelian</button>
                <button x-on:click="tab = 'modal'" :class="tab === 'modal' ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                    class="flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition">Modal</button>
            </div>

            <div x-show="tab !== 'modal'" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Dari</label>
                    <input type="date" x-model="dari" class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-red-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Sampai</label>
                    <input type="date" x-model="sampai" class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-red-400">
                </div>
                <button x-on:click="dari=''; sampai=''" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Reset</button>
                <div class="sm:ml-auto flex gap-2">
                    <button x-on:click="exportPDF()" class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4"></i> PDF
                    </button>
                    <button x-on:click="exportExcel()" class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <i data-lucide="sheet" class="h-4 w-4"></i> Excel
                    </button>
                </div>
            </div>

            <div x-show="tab === 'jual'" x-cloak class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400">
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">Invoice</th>
                                <th class="px-4 py-3 font-medium">Produk</th>
                                <th class="px-4 py-3 font-medium">Qty</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(r, i) in filterTanggal(rows)" :key="i">
                                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500" x-text="fmtTgl(r.tanggal)"></td>
                                    <td class="px-4 py-3 font-medium" x-text="r.invoice"></td>
                                    <td class="px-4 py-3" x-text="r.produk"></td>
                                    <td class="px-4 py-3" x-text="r.jumlah"></td>
                                    <td class="px-4 py-3" x-text="rupiah(r.harga)"></td>
                                    <td class="px-4 py-3 font-semibold" x-text="rupiah(r.total)"></td>
                                    <td class="px-4 py-3"><span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold" x-text="r.pembayaran"></span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div x-show="filterTanggal(rows).length === 0" class="py-14 text-center text-sm text-gray-400">Belum ada penjualan.</div>
            </div>

            <div x-show="tab === 'beli'" x-cloak class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400">
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">Faktur</th>
                                <th class="px-4 py-3 font-medium">Produk</th>
                                <th class="px-4 py-3 font-medium">Qty</th>
                                <th class="px-4 py-3 font-medium">Harga Beli</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(r, i) in filterTanggal(pembelian)" :key="i">
                                <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500" x-text="fmtTgl(r.tanggal)"></td>
                                    <td class="px-4 py-3 font-medium" x-text="r.faktur"></td>
                                    <td class="px-4 py-3" x-text="r.produk"></td>
                                    <td class="px-4 py-3" x-text="r.jumlah"></td>
                                    <td class="px-4 py-3" x-text="rupiah(r.harga)"></td>
                                    <td class="px-4 py-3 font-semibold" x-text="rupiah(r.total)"></td>
                                    <td class="px-4 py-3 text-gray-600" x-text="r.supplier"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div x-show="filterTanggal(pembelian).length === 0" class="py-14 text-center text-sm text-gray-400">Belum ada pembelian.</div>
            </div>

            <div x-show="tab === 'modal'" x-cloak class="mt-4">
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm text-gray-500">Modal awal & tambahan modal usaha.</p>
                    <button x-on:click="openModal()" class="flex items-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        <i data-lucide="plus" class="h-4 w-4"></i> Tambah Modal
                    </button>
                </div>
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px] text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400">
                                    <th class="px-4 py-3 font-medium">Tanggal</th>
                                    <th class="px-4 py-3 font-medium">Jenis</th>
                                    <th class="px-4 py-3 font-medium">Jumlah</th>
                                    <th class="px-4 py-3 font-medium">Catatan</th>
                                    <th class="px-4 py-3 text-right font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="m in modalRows" :key="m.id">
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-500" x-text="fmtTgl(m.date)"></td>
                                        <td class="px-4 py-3">
                                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                                :class="m.type === 'awal' ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600'"
                                                x-text="m.type === 'awal' ? 'Modal Awal' : 'Tambahan'"></span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold" x-text="rupiah(m.amount)"></td>
                                        <td class="px-4 py-3 text-gray-600" x-text="m.note || '-'"></td>
                                        <td class="px-4 py-3 text-right">
                                            <button x-on:click="hapusModal(m)" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600">
                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div x-show="modalRows.length === 0" class="py-14 text-center text-sm text-gray-400">Belum ada data modal.</div>
                </div>
            </div>
        </div>
    </main>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold">Tambah Modal</h3>
                <button x-on:click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <label class="mb-1 block text-xs font-medium text-gray-500">Jenis</label>
            <select x-model="mform.type" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400">
                <option value="tambahan">Tambahan Modal</option>
                <option value="awal" @if($punyaAwal) disabled @endif>Modal Awal @if($punyaAwal)(sudah diisi)@endif</option>
            </select>

            <label class="mb-1 mt-3 block text-xs font-medium text-gray-500">Jumlah</label>
            <input type="number" x-model="mform.amount" min="1" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

            <label class="mb-1 mt-3 block text-xs font-medium text-gray-500">Tanggal</label>
            <input type="date" x-model="mform.date" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400">

            <label class="mb-1 mt-3 block text-xs font-medium text-gray-500">Catatan (opsional)</label>
            <input x-model="mform.note" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">

            <div class="mt-5 flex gap-3">
                <button x-on:click="showModal = false" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button x-on:click="saveModal()" :disabled="saving" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:bg-gray-300">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pembukuan', () => ({
            rows: @json($rows),
            pembelian: @json($pembelian),
            modalRows: @json($modalRows),
            csrf: '{{ csrf_token() }}',

            sidebarOpen: false,
            tab: 'jual',
            dari: '',
            sampai: '',
            saving: false,

            showModal: false,
            mform: { type: 'tambahan', amount: 0, date: '{{ date('Y-m-d') }}', note: '' },

            rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            fmtTgl(t) {
                if (!t) return '-';
                const d = new Date(t);
                if (isNaN(d)) return t;
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            },
            filterTanggal(list) {
                return list.filter(r => {
                    const t = (r.tanggal || '').slice(0, 10);
                    if (this.dari && t < this.dari) return false;
                    if (this.sampai && t > this.sampai) return false;
                    return true;
                });
            },

            openModal() {
                this.mform = { type: 'tambahan', amount: 0, date: '{{ date('Y-m-d') }}', note: '' };
                this.showModal = true;
            },
            async saveModal() {
                if (!this.mform.amount || this.mform.amount < 1) { alert('Jumlah modal wajib diisi.'); return; }
                this.saving = true;
                try {
                    const res = await fetch('/pembukuan/modal', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            type: this.mform.type,
                            amount: Number(this.mform.amount) || 0,
                            date: this.mform.date,
                            note: this.mform.note || null,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menyimpan modal.'); this.saving = false; return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); this.saving = false; }
            },
            async hapusModal(m) {
                if (!confirm('Hapus data modal ini?')) return;
                try {
                    const res = await fetch('/pembukuan/modal/hapus', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ id: m.id }),
                    });
                    const data = await res.json();
                    if (!res.ok) { alert(data.message || 'Gagal menghapus.'); return; }
                    location.reload();
                } catch (e) { alert('Koneksi ke server gagal.'); }
            },

            exportPDF() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                const isJual = this.tab === 'jual';
                const judul = isJual ? 'Riwayat Penjualan' : 'Riwayat Pembelian';
                doc.setFontSize(14);
                doc.text('Dewanda Motor — ' + judul, 14, 16);
                doc.setFontSize(9);
                doc.text('Dicetak: ' + new Date().toLocaleString('id-ID'), 14, 22);

                let head, body;
                if (isJual) {
                    head = [['Tanggal', 'Invoice', 'Produk', 'Qty', 'Harga', 'Total', 'Bayar']];
                    body = this.filterTanggal(this.rows).map(r => [
                        this.fmtTgl(r.tanggal), r.invoice, r.produk, r.jumlah,
                        this.rupiah(r.harga), this.rupiah(r.total), r.pembayaran,
                    ]);
                } else {
                    head = [['Tanggal', 'Faktur', 'Produk', 'Qty', 'Harga Beli', 'Total', 'Supplier']];
                    body = this.filterTanggal(this.pembelian).map(r => [
                        this.fmtTgl(r.tanggal), r.faktur, r.produk, r.jumlah,
                        this.rupiah(r.harga), this.rupiah(r.total), r.supplier,
                    ]);
                }
                doc.autoTable({ head, body, startY: 28, styles: { fontSize: 8 }, headStyles: { fillColor: [224, 30, 38] } });
                doc.save('dewanda-' + (isJual ? 'penjualan' : 'pembelian') + '.pdf');
            },
            exportExcel() {
                const isJual = this.tab === 'jual';
                let data;
                if (isJual) {
                    data = this.filterTanggal(this.rows).map(r => ({
                        Tanggal: this.fmtTgl(r.tanggal), Invoice: r.invoice, Produk: r.produk,
                        Qty: r.jumlah, Harga: r.harga, Total: r.total, Bayar: r.pembayaran,
                    }));
                } else {
                    data = this.filterTanggal(this.pembelian).map(r => ({
                        Tanggal: this.fmtTgl(r.tanggal), Faktur: r.faktur, Produk: r.produk,
                        Qty: r.jumlah, HargaBeli: r.harga, Total: r.total, Supplier: r.supplier,
                    }));
                }
                const ws = XLSX.utils.json_to_sheet(data);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, isJual ? 'Penjualan' : 'Pembelian');
                XLSX.writeFile(wb, 'dewanda-' + (isJual ? 'penjualan' : 'pembelian') + '.xlsx');
            },
        }));
    });
</script>
@endpush