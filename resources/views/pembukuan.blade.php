@extends('layouts.app')

@section('title', 'Pembukuan · Dewanda Motor')

@section('content')
<div x-data="pembukuan()" class="flex h-screen flex-col overflow-hidden bg-gray-50">

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
                <a href="/pembukuan" class="flex w-full items-center gap-3 rounded-xl bg-red-50 px-3 py-2.5 text-sm font-medium text-red-600">
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

        <main class="flex-1 overflow-y-auto p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="flex items-center gap-2 text-xl font-bold"><i data-lucide="book-open" class="h-6 w-6"></i> Pembukuan</h2>
                    <p class="text-sm text-gray-400">Catatan Penjualan & Pembelian Sparepart</p>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button x-on:click="open = !open"
                        class="flex items-center gap-2 rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                        <i data-lucide="download" class="h-4 w-4"></i> Export <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </button>
                    <div x-show="open" x-cloak x-on:click.outside="open = false"
                        class="absolute right-0 z-10 mt-2 w-44 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                        <button x-on:click="exportPDF(); open = false" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm hover:bg-gray-50"><i data-lucide="file-text" class="h-4 w-4 text-red-600"></i> Export PDF</button>
                        <button x-on:click="exportExcel(); open = false" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm hover:bg-gray-50"><i data-lucide="sheet" class="h-4 w-4 text-green-600"></i> Export Excel</button>
                    </div>
                </div>
            </div>

            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500">Dari</label>
                        <input type="date" x-model="dari" x-on:change="page = 1" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500">Ke</label>
                        <input type="date" x-model="ke" x-on:change="page = 1" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-500">Metode</label>
                        <select x-model="metode" x-on:change="page = 1" :disabled="tab==='beli'" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 disabled:opacity-50">
                            <option value="semua">Semua</option>
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button x-on:click="page = 1" class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            <i data-lucide="filter" class="h-4 w-4"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button x-on:click="setPeriode('hari')"  class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:border-red-300 hover:text-red-600">Hari Ini</button>
                    <button x-on:click="setPeriode('bulan')" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:border-red-300 hover:text-red-600">Bulan Ini</button>
                    <button x-on:click="setPeriode('tahun')" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:border-red-300 hover:text-red-600">Tahun Ini</button>
                    <button x-on:click="dari=''; ke=''; page=1" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:border-red-300 hover:text-red-600">Semua</button>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500"><i data-lucide="arrow-down-left" class="h-4 w-4 text-green-600"></i><span class="text-sm">Total Pemasukan</span></div>
                    <p class="text-2xl font-bold" x-text="rupiah(pemasukan)"></p>
                    <p class="mt-1 text-xs text-green-600">▲ 12,4% vs periode lalu</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500"><i data-lucide="arrow-up-right" class="h-4 w-4 text-red-600"></i><span class="text-sm">Total Pengeluaran</span></div>
                    <p class="text-2xl font-bold" x-text="rupiah(pengeluaran)"></p>
                    <p class="mt-1 text-xs text-red-600">▼ 8,1% vs periode lalu</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="mb-1 flex items-center gap-2 text-gray-500"><i data-lucide="wallet" class="h-4 w-4 text-red-600"></i><span class="text-sm">Saldo Bersih</span></div>
                    <p class="text-2xl font-bold text-red-600" x-text="rupiah(saldo)"></p>
                    <p class="mt-1 text-xs text-green-600">▲ 5,2% vs periode lalu</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white">
                {{-- Tab --}}
                <div class="flex gap-6 border-b border-gray-100 px-5 pt-4">
                    <button x-on:click="tab='jual'; page=1"
                        :class="tab==='jual' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="border-b-2 pb-3 text-sm font-semibold transition">Riwayat Penjualan</button>
                    <button x-on:click="tab='beli'; page=1"
                        :class="tab==='beli' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="border-b-2 pb-3 text-sm font-semibold transition">Riwayat Pembelian</button>
                </div>

                {{-- TABEL PENJUALAN --}}
                <div x-show="tab==='jual'" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">No</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">Invoice</th>
                                <th class="px-4 py-3 font-medium">Produk</th>
                                <th class="px-4 py-3 font-medium">Jumlah</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(r, idx) in paged()" :key="'j'+idx">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500" x-text="(page - 1) * perPage + idx + 1"></td>
                                    <td class="px-4 py-3" x-text="fmtTgl(r.tanggal)"></td>
                                    <td class="px-4 py-3 text-gray-500" x-text="r.invoice"></td>
                                    <td class="px-4 py-3 font-medium" x-text="r.produk"></td>
                                    <td class="px-4 py-3" x-text="r.jumlah"></td>
                                    <td class="px-4 py-3" x-text="rupiah(r.harga)"></td>
                                    <td class="px-4 py-3 font-semibold" x-text="rupiah(r.total)"></td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-md px-2.5 py-1 text-xs font-semibold"
                                            :class="r.pembayaran === 'Cash' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600'"
                                            x-text="r.pembayaran"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="totalData === 0">
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">Tidak ada data pada periode ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABEL PEMBELIAN --}}
                <div x-show="tab==='beli'" x-cloak class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">No</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">No. Faktur</th>
                                <th class="px-4 py-3 font-medium">Produk</th>
                                <th class="px-4 py-3 font-medium">Jumlah</th>
                                <th class="px-4 py-3 font-medium">Harga Beli</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Supplier</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(r, idx) in paged()" :key="'b'+idx">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500" x-text="(page - 1) * perPage + idx + 1"></td>
                                    <td class="px-4 py-3" x-text="fmtTgl(r.tanggal)"></td>
                                    <td class="px-4 py-3 text-gray-500" x-text="r.faktur"></td>
                                    <td class="px-4 py-3 font-medium" x-text="r.produk"></td>
                                    <td class="px-4 py-3" x-text="r.jumlah"></td>
                                    <td class="px-4 py-3" x-text="rupiah(r.harga)"></td>
                                    <td class="px-4 py-3 font-semibold" x-text="rupiah(r.total)"></td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-600" x-text="r.supplier"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="totalData === 0">
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400">Tidak ada data pada periode ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination (dipakai kedua tab) --}}
                <div class="flex flex-col gap-3 border-t border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <span class="text-xs text-gray-500"
                        x-text="'Menampilkan ' + (totalData === 0 ? 0 : ((page-1)*perPage + 1)) + '-' + Math.min(page*perPage, totalData) + ' dari ' + totalData + ' data'"></span>
                    <div class="flex items-center gap-1.5">
                        <button x-on:click="goPage(page-1)" :disabled="page <= 1"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-sm hover:bg-gray-50 disabled:opacity-40">‹</button>

                        <template x-for="(p, i) in pageList()" :key="i">
                            <span>
                                <button x-show="p !== '...'" x-on:click="goPage(p)"
                                    :class="p === page ? 'border-red-600 bg-red-600 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'"
                                    class="h-8 min-w-8 rounded-lg border px-2 text-sm font-medium"
                                    x-text="p"></button>
                                <span x-show="p === '...'" class="px-1 text-sm text-gray-400">…</span>
                            </span>
                        </template>

                        <button x-on:click="goPage(page+1)" :disabled="page >= totalPage"
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-sm hover:bg-gray-50 disabled:opacity-40">›</button>
                    </div>
                </div>
            </div>
        </main>
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
            tab: 'jual',
            dari: '',
            ke: '',
            metode: 'semua',
            page: 1,
            perPage: 10,

            rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            fmtTgl(iso) { return new Date(iso).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }); },

            sumber() { return this.tab === 'beli' ? this.pembelian : this.rows; },
            filtered() {
                return this.sumber().filter(r => {
                    const okMetode = this.tab === 'beli' || this.metode === 'semua' || r.pembayaran.toLowerCase() === this.metode;
                    const okDari = !this.dari || r.tanggal >= this.dari;
                    const okKe = !this.ke || r.tanggal <= this.ke;
                    return okMetode && okDari && okKe;
                });
            },
            // Pemasukan = total penjualan, Pengeluaran = total pembelian (nyata dari data pembelian)
            get pemasukan()   { return this.rows.reduce((s, r) => s + r.total, 0); },
            get pengeluaran() { return this.pembelian.reduce((s, r) => s + r.total, 0); },
            get saldo()       { return this.pemasukan - this.pengeluaran; },
            get totalData()   { return this.filtered().length; },
            get totalPage()   { return Math.max(1, Math.ceil(this.totalData / this.perPage)); },

            paged() {
                const f = this.filtered();
                const start = (this.page - 1) * this.perPage;
                return f.slice(start, start + this.perPage);
            },
            goPage(p) { if (p >= 1 && p <= this.totalPage) this.page = p; },

            pageList() {
                const total = this.totalPage;
                const cur = this.page;
                const arr = [];
                if (total <= 7) {
                    for (let i = 1; i <= total; i++) arr.push(i);
                    return arr;
                }
                arr.push(1);
                if (cur > 3) arr.push('...');
                const start = Math.max(2, cur - 1);
                const end = Math.min(total - 1, cur + 1);
                for (let i = start; i <= end; i++) arr.push(i);
                if (cur < total - 2) arr.push('...');
                arr.push(total);
                return arr;
            },

            setPeriode(type) {
                const now = new Date();
                const pad = n => String(n).padStart(2, '0');
                const iso = d => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
                if (type === 'hari')  { this.dari = iso(now); this.ke = iso(now); }
                if (type === 'bulan') { this.dari = iso(new Date(now.getFullYear(), now.getMonth(), 1)); this.ke = iso(new Date(now.getFullYear(), now.getMonth() + 1, 0)); }
                if (type === 'tahun') { this.dari = now.getFullYear() + '-01-01'; this.ke = now.getFullYear() + '-12-31'; }
                this.page = 1;
            },

            exportPDF() {
                const rows = this.filtered();
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                const rp = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
                const now = new Date();
                const judul = this.tab === 'beli' ? 'Riwayat Pembelian' : 'Riwayat Penjualan';

                doc.setFontSize(14); doc.setFont(undefined, 'bold');
                doc.text('Laporan Pembukuan Dewanda Motor', 14, 16);
                doc.setFontSize(9); doc.setFont(undefined, 'normal');
                doc.text(judul + ' Sparepart', 14, 22);
                doc.text('Dicetak: ' + now.toLocaleString('id-ID'), 14, 27);
                doc.text('Periode: ' + (this.dari || 'awal') + ' s/d ' + (this.ke || 'akhir'), 14, 32);

                doc.setFontSize(10);
                doc.text('Total Pemasukan   : ' + rp(this.pemasukan), 14, 42);
                doc.text('Total Pengeluaran : ' + rp(this.pengeluaran), 14, 47);
                doc.setFont(undefined, 'bold');
                doc.text('Saldo Bersih      : ' + rp(this.saldo), 14, 52);
                doc.setFont(undefined, 'normal');

                let head, body;
                if (this.tab === 'beli') {
                    head = [['No', 'Tanggal', 'Faktur', 'Produk', 'Jumlah', 'Harga Beli', 'Total', 'Supplier']];
                    body = rows.map((r, i) => [i + 1, this.fmtTgl(r.tanggal), r.faktur, r.produk, r.jumlah, rp(r.harga), rp(r.total), r.supplier]);
                } else {
                    head = [['No', 'Tanggal', 'Invoice', 'Produk', 'Jumlah', 'Harga', 'Total', 'Pembayaran']];
                    body = rows.map((r, i) => [i + 1, this.fmtTgl(r.tanggal), r.invoice, r.produk, r.jumlah, rp(r.harga), rp(r.total), r.pembayaran]);
                }

                doc.autoTable({
                    startY: 58,
                    head: head,
                    body: body,
                    styles: { fontSize: 8, cellPadding: 2 },
                    headStyles: { fillColor: [220, 38, 38] },
                    columnStyles: { 5: { halign: 'right' }, 6: { halign: 'right' } },
                });

                doc.save('pembukuan-' + this.tab + '-dewanda-motor.pdf');
            },

            exportExcel() {
                const rows = this.filtered();
                const judul = this.tab === 'beli' ? 'Riwayat Pembelian' : 'Riwayat Penjualan';

                let data;
                if (this.tab === 'beli') {
                    data = rows.map((r, i) => ({
                        'No': i + 1, 'Tanggal': this.fmtTgl(r.tanggal), 'Faktur': r.faktur,
                        'Produk': r.produk, 'Jumlah': r.jumlah, 'Harga Beli': r.harga,
                        'Total': r.total, 'Supplier': r.supplier,
                    }));
                } else {
                    data = rows.map((r, i) => ({
                        'No': i + 1, 'Tanggal': this.fmtTgl(r.tanggal), 'Invoice': r.invoice,
                        'Produk': r.produk, 'Jumlah': r.jumlah, 'Harga': r.harga,
                        'Total': r.total, 'Pembayaran': r.pembayaran,
                    }));
                }

                const header = [
                    ['Laporan Pembukuan Dewanda Motor'],
                    [judul + ' Sparepart'],
                    ['Total Pemasukan', this.pemasukan],
                    ['Total Pengeluaran', this.pengeluaran],
                    ['Saldo Bersih', this.saldo],
                    [],
                ];

                const ws = XLSX.utils.aoa_to_sheet(header);
                XLSX.utils.sheet_add_json(ws, data, { origin: 'A7' });
                ws['!cols'] = [{ wch: 5 }, { wch: 16 }, { wch: 18 }, { wch: 26 }, { wch: 8 }, { wch: 12 }, { wch: 14 }, { wch: 16 }];

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Pembukuan');
                XLSX.writeFile(wb, 'pembukuan-' + this.tab + '-dewanda-motor.xlsx');
            },
        }));
    });
</script>
@endpush