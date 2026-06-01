@extends('layouts.app')
@section('title', 'Laporan Presensi')

@section('content')
<div class="space-y-6 print:space-y-4">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 pb-5 print:pb-2 print:border-b-2 print:border-slate-800">
        <div>
            <h1 id="report-title" class="text-2xl font-black text-slate-800 tracking-tight print:text-xl print:font-bold">Laporan Rekapitulasi Presensi</h1>
            <p id="report-subtitle" class="text-sm text-slate-500 font-medium print:hidden">Pantau dan cetak rekap kehadiran mahasiswa secara komprehensif.</p>
            <div class="hidden print:block text-xs text-slate-600 mt-1 font-mono">
                Dicetak pada: <span id="print-date"></span> | Sistem PresensiApp
            </div>
        </div>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto print:hidden">
            <button onclick="exportToExcel()" class="w-full xs:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-2 group active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor ke Excel
            </button>
            <button onclick="window.print()" class="w-full xs:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2 group active:scale-95 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Laporan (PDF)
            </button>
        </div>
    </div>

    <!-- Report Type Tabs (Hidden in Print, Only shown to Admin) -->
    <div id="admin-report-tabs" class="hidden print:hidden border-b border-slate-200 my-2">
        <div class="flex flex-wrap -mb-px text-xs sm:text-sm font-semibold text-center text-slate-500">
            <button onclick="switchReportTab('presensi')" class="report-tab active-tab mr-1 sm:mr-2 inline-flex items-center gap-1.5 px-3 sm:px-4 py-2.5 border-b-2 border-indigo-600 text-indigo-600 rounded-t-lg transition-all cursor-pointer" data-type="presensi">
                📊 Rekap Presensi
            </button>
            <button onclick="switchReportTab('mahasiswa')" class="report-tab mr-1 sm:mr-2 inline-flex items-center gap-1.5 px-3 sm:px-4 py-2.5 border-b-2 border-transparent hover:text-slate-700 hover:border-slate-300 rounded-t-lg transition-all cursor-pointer" data-type="mahasiswa">
                🎓 Mahasiswa
            </button>
            <button onclick="switchReportTab('dosen')" class="report-tab mr-1 sm:mr-2 inline-flex items-center gap-1.5 px-3 sm:px-4 py-2.5 border-b-2 border-transparent hover:text-slate-700 hover:border-slate-300 rounded-t-lg transition-all cursor-pointer" data-type="dosen">
                💼 Dosen
            </button>
            <button onclick="switchReportTab('matkul')" class="report-tab mr-1 sm:mr-2 inline-flex items-center gap-1.5 px-3 sm:px-4 py-2.5 border-b-2 border-transparent hover:text-slate-700 hover:border-slate-300 rounded-t-lg transition-all cursor-pointer" data-type="matkul">
                📖 Mata Kuliah
            </button>
            <button onclick="switchReportTab('gedung')" class="report-tab mr-1 sm:mr-2 inline-flex items-center gap-1.5 px-3 sm:px-4 py-2.5 border-b-2 border-transparent hover:text-slate-700 hover:border-slate-300 rounded-t-lg transition-all cursor-pointer" data-type="gedung">
                🏢 Gedung
            </button>
            <button onclick="switchReportTab('jurusan')" class="report-tab mr-1 sm:mr-2 inline-flex items-center gap-1.5 px-3 sm:px-4 py-2.5 border-b-2 border-transparent hover:text-slate-700 hover:border-slate-300 rounded-t-lg transition-all cursor-pointer" data-type="jurusan">
                🏛️ Jurusan
            </button>
        </div>
    </div>

    <!-- Filters Section (Hidden in print) -->
    <div id="filters-section" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 print:hidden">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.586A1 1 0 013 6.586V4z"></path></svg>
            Filter Data Rekapitulasi
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter Tahun Ajaran -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun Ajaran</label>
                <select id="filter-tahun-ajaran" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-white font-medium">
                    <option value="">Semua Tahun Ajaran</option>
                    <option value="2023/2024" selected>2023/2024</option>
                    <option value="2024/2025">2024/2025</option>
                    <option value="2025/2026">2025/2026</option>
                    <option value="2026/2027">2026/2027</option>
                </select>
            </div>

            <!-- Filter Jurusan -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jurusan</label>
                <select id="filter-jurusan" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-white font-medium">
                    <option value="">Semua Jurusan</option>
                </select>
            </div>

            <!-- Filter Mata Kuliah -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Mata Kuliah</label>
                <select id="filter-mata-kuliah" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-white font-medium">
                    <option value="">Semua Mata Kuliah</option>
                </select>
            </div>

            <!-- Filter Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Rentang Tanggal</label>
                <div class="flex gap-2">
                    <input type="date" id="filter-start-date" class="w-1/2 rounded-lg border border-slate-300 px-2 py-1.5 text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-slate-400 self-center text-xs">s/d</span>
                    <input type="date" id="filter-end-date" class="w-1/2 rounded-lg border border-slate-300 px-2 py-1.5 text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Active Filters Metadata (Visible in print) -->
    <div id="print-meta-section" class="hidden print:grid grid-cols-2 gap-4 border border-slate-300 p-4 rounded-xl text-xs bg-slate-50 font-mono">
        <div>
            <span class="font-bold text-slate-700">Tahun Ajaran:</span> <span id="print-meta-ta">-</span>
        </div>
        <div>
            <span class="font-bold text-slate-700">Mata Kuliah:</span> <span id="print-meta-mk">-</span>
        </div>
        <div>
            <span class="font-bold text-slate-700">Jurusan:</span> <span id="print-meta-jurusan">-</span>
        </div>
        <div>
            <span class="font-bold text-slate-700">Rentang Tanggal:</span> <span id="print-meta-tgl">-</span>
        </div>
    </div>

    <!-- Statistics Grid Cards (Visuals hidden or minimal in print) -->
    <div id="statistics-section" class="grid grid-cols-2 lg:grid-cols-4 gap-4 print:grid-cols-4 print:gap-2">
        <!-- Card Total Mahasiswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between group overflow-hidden relative print:p-3 print:border-slate-300">
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-slate-400 print:text-[10px]">Total Mahasiswa</p>
                <p class="text-3xl font-extrabold text-slate-800 mt-2 font-mono group-hover:scale-105 transition-transform origin-left print:text-xl print:mt-1" id="stat-total-students">0</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-inner print:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <!-- Card Rata-Rata Kehadiran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between group overflow-hidden relative print:p-3 print:border-slate-300">
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-slate-400 print:text-[10px]">Rata-Rata Hadir</p>
                <p class="text-3xl font-extrabold text-slate-800 mt-2 font-mono group-hover:scale-105 transition-transform origin-left print:text-xl print:mt-1" id="stat-avg-attendance">0%</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-inner print:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Card Total Sakit & Izin -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between group overflow-hidden relative print:p-3 print:border-slate-300">
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-slate-400 print:text-[10px]">Sakit / Izin</p>
                <p class="text-3xl font-extrabold text-slate-800 mt-2 font-mono group-hover:scale-105 transition-transform origin-left print:text-xl print:mt-1" id="stat-excused">0 / 0</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all shadow-inner print:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>

        <!-- Card Total Alpha -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between group overflow-hidden relative print:p-3 print:border-slate-300">
            <div>
                <p class="text-xs font-black uppercase tracking-wider text-slate-400 print:text-[10px]">Total Tanpa Keterangan</p>
                <p class="text-3xl font-extrabold text-slate-800 mt-2 font-mono group-hover:scale-105 transition-transform origin-left print:text-xl print:mt-1" id="stat-alpha">0</p>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-all shadow-inner print:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Attendance Rekap Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm print:border-none print:shadow-none print:rounded-none">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between print:hidden">
            <h2 class="text-base font-bold text-slate-800">Daftar Kehadiran Kumulatif</h2>
            <span class="text-xs text-slate-400 italic">Data ter-update secara otomatis sesuai filter</span>
        </div>
        <div class="p-0 sm:p-6 print:p-0">
            <div class="overflow-x-auto">
                <table id="laporan-table" class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase tracking-wider text-[10px] print:bg-slate-200 print:text-slate-900 print:border-slate-800">
                        <tr>
                            <th class="p-4 print:p-2">NIM</th>
                            <th class="p-4 print:p-2">Nama Mahasiswa</th>
                            <th class="p-4 print:p-2">Jurusan</th>
                            <th class="p-4 print:p-2">Mata Kuliah</th>
                            <th class="p-4 print:p-2 text-center">Sesi</th>
                            <th class="p-4 print:p-2 text-center bg-emerald-50/50 text-emerald-700 print:bg-transparent print:text-slate-800">Hadir</th>
                            <th class="p-4 print:p-2 text-center bg-amber-50/50 text-amber-700 print:bg-transparent print:text-slate-800">Izin</th>
                            <th class="p-4 print:p-2 text-center bg-sky-50/50 text-sky-700 print:bg-transparent print:text-slate-800">Sakit</th>
                            <th class="p-4 print:p-2 text-center bg-rose-50/50 text-rose-700 print:bg-transparent print:text-slate-800">Alpha</th>
                            <th class="p-4 print:p-2 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 print:divide-y print:divide-slate-300">
                        <!-- Dynamic Rows -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        /* Reset layout container limitations to fix PDF clipping */
        html, body, #app-body, .flex, main, .flex-grow, #main-content {
            overflow: visible !important;
            height: auto !important;
            min-height: auto !important;
            display: block !important;
            position: static !important;
        }
        
        .print\:hidden {
            display: none !important;
        }
        
        aside, nav, header, footer, #sidebar, #top-bar, #sidebar-backdrop {
            display: none !important;
        }
        
        main, #main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: 1px solid #1e293b !important;
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }
        
        th, td {
            border: 1px solid #94a3b8 !important;
            padding: 6px !important;
            color: black !important;
            background: transparent !important;
        }
        
        thead {
            display: table-header-group !important;
        }
    }
</style>
@endpush

@push('scripts')
<!-- SheetJS Library for Excel Export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    let laporanTable;
    const currentUser = JSON.parse(localStorage.getItem('user'));
    let currentTab = 'presensi';
    let activeData = []; // Store currently loaded dataset for Excel export

    // Headers templates for each tab
    const headersConfig = {
        presensi: `
            <tr>
                <th class="p-4 print:p-2">NIM</th>
                <th class="p-4 print:p-2">Nama Mahasiswa</th>
                <th class="p-4 print:p-2">Jurusan</th>
                <th class="p-4 print:p-2">Mata Kuliah</th>
                <th class="p-4 print:p-2 text-center">Sesi</th>
                <th class="p-4 print:p-2 text-center bg-emerald-50/50 text-emerald-700 print:bg-transparent print:text-slate-800">Hadir</th>
                <th class="p-4 print:p-2 text-center bg-amber-50/50 text-amber-700 print:bg-transparent print:text-slate-800">Izin</th>
                <th class="p-4 print:p-2 text-center bg-sky-50/50 text-sky-700 print:bg-transparent print:text-slate-800">Sakit</th>
                <th class="p-4 print:p-2 text-center bg-rose-50/50 text-rose-700 print:bg-transparent print:text-slate-800">Alpha</th>
                <th class="p-4 print:p-2 text-right">Persentase</th>
            </tr>
        `,
        mahasiswa: `
            <tr>
                <th class="p-4 print:p-2">NIM</th>
                <th class="p-4 print:p-2">Nama Mahasiswa</th>
                <th class="p-4 print:p-2">Email</th>
                <th class="p-4 print:p-2">Jurusan</th>
                <th class="p-4 print:p-2 text-center">Angkatan</th>
            </tr>
        `,
        dosen: `
            <tr>
                <th class="p-4 print:p-2">NIP</th>
                <th class="p-4 print:p-2">Nama Dosen</th>
                <th class="p-4 print:p-2">Email</th>
                <th class="p-4 print:p-2">Jurusan</th>
            </tr>
        `,
        matkul: `
            <tr>
                <th class="p-4 print:p-2">Kode MK</th>
                <th class="p-4 print:p-2">Nama Mata Kuliah</th>
                <th class="p-4 print:p-2 text-center">SKS</th>
                <th class="p-4 print:p-2 text-center">Semester</th>
                <th class="p-4 print:p-2">Dosen Pengampu</th>
            </tr>
        `,
        gedung: `
            <tr>
                <th class="p-4 print:p-2">Kode Gedung</th>
                <th class="p-4 print:p-2">Nama Gedung</th>
                <th class="p-4 print:p-2">Lokasi</th>
            </tr>
        `,
        jurusan: `
            <tr>
                <th class="p-4 print:p-2">Kode Jurusan</th>
                <th class="p-4 print:p-2">Nama Jurusan</th>
            </tr>
        `
    };

    // Columns config for DataTables
    const columnsConfig = {
        presensi: [
            { data: 'nim', className: 'p-4 print:p-2 font-mono text-xs font-semibold text-slate-800' },
            { data: 'nama_mahasiswa', className: 'p-4 print:p-2 font-bold text-slate-800' },
            { data: 'jurusan', className: 'p-4 print:p-2 text-xs text-slate-500' },
            { 
                data: null, 
                className: 'p-4 print:p-2',
                render: row => `<div class="flex flex-col"><span class="font-medium text-slate-800 print:font-semibold">${row.mata_kuliah}</span><span class="text-[9px] text-slate-400 font-mono">${row.kode_mk}</span></div>`
            },
            { data: 'total_sesi', className: 'p-4 print:p-2 text-center font-semibold text-slate-700 font-mono' },
            { data: 'hadir', className: 'p-4 print:p-2 text-center font-bold text-emerald-600 font-mono bg-emerald-50/20 print:bg-transparent' },
            { data: 'izin', className: 'p-4 print:p-2 text-center font-semibold text-amber-600 font-mono bg-amber-50/20 print:bg-transparent' },
            { data: 'sakit', className: 'p-4 print:p-2 text-center font-semibold text-sky-600 font-mono bg-sky-50/20 print:bg-transparent' },
            { data: 'alpha', className: 'p-4 print:p-2 text-center font-bold text-rose-600 font-mono bg-rose-50/20 print:bg-transparent' },
            { 
                data: 'persentase', 
                className: 'p-4 print:p-2 text-right font-mono font-black',
                render: val => {
                    let color = 'text-emerald-600';
                    if (val < 60) color = 'text-rose-600';
                    else if (val < 80) color = 'text-amber-600';
                    return `<span class="${color}">${val}%</span>`;
                }
            }
        ],
        mahasiswa: [
            { data: 'nim', className: 'p-4 print:p-2 font-mono text-xs font-semibold text-slate-800' },
            { data: 'user.nama', className: 'p-4 print:p-2 font-bold text-slate-800', defaultContent: '-' },
            { data: 'user.email', className: 'p-4 print:p-2 text-xs text-slate-500', defaultContent: '-' },
            { data: 'jurusan.nama', className: 'p-4 print:p-2 text-xs text-slate-600', defaultContent: '-' },
            { data: 'angkatan', className: 'p-4 print:p-2 font-semibold text-slate-700 font-mono text-center font-bold', defaultContent: '-' }
        ],
        dosen: [
            { data: 'nip', className: 'p-4 print:p-2 font-mono text-xs font-semibold text-slate-800', defaultContent: '-' },
            { data: 'user.nama', className: 'p-4 print:p-2 font-bold text-slate-800', defaultContent: '-' },
            { data: 'user.email', className: 'p-4 print:p-2 text-xs text-slate-500', defaultContent: '-' },
            { data: 'jurusan.nama', className: 'p-4 print:p-2 text-xs text-slate-600', defaultContent: '-' }
        ],
        matkul: [
            { data: 'kode', className: 'p-4 print:p-2 font-mono text-xs font-semibold text-slate-800' },
            { data: 'nama', className: 'p-4 print:p-2 font-bold text-slate-800' },
            { data: 'sks', className: 'p-4 print:p-2 text-center font-semibold text-slate-700 font-mono font-bold' },
            { data: 'semester', className: 'p-4 print:p-2 text-center font-semibold text-slate-700 font-mono' },
            { data: 'dosen.user.nama', className: 'p-4 print:p-2 text-xs text-slate-600', defaultContent: '-' }
        ],
        gedung: [
            { data: 'kode', className: 'p-4 print:p-2 font-mono text-xs font-semibold text-slate-800', defaultContent: '-' },
            { data: 'nama', className: 'p-4 print:p-2 font-bold text-slate-800', defaultContent: '-' },
            { data: 'lokasi', className: 'p-4 print:p-2 text-xs text-slate-600', defaultContent: '-' }
        ],
        jurusan: [
            { data: 'kode', className: 'p-4 print:p-2 font-mono text-xs font-semibold text-slate-800' },
            { data: 'nama', className: 'p-4 print:p-2 font-bold text-slate-800' }
        ]
    };

    $(document).ready(function() {
        // Set date in print view
        $('#print-date').text(new Date().toLocaleString('id-ID'));

        // If Admin, show report type tabs
        if (currentUser && currentUser.role === 'admin') {
            $('#admin-report-tabs').removeClass('hidden');
        }

        // Load distinct options for Jurusan and Mata Kuliah to populate filters
        loadFilterDropdowns();

        // Initialize with Rekap Presensi
        switchReportTab('presensi');

        // Trigger loading data on change of filters
        $('#filter-tahun-ajaran, #filter-jurusan, #filter-mata-kuliah, #filter-start-date, #filter-end-date').on('change', function() {
            if (currentTab === 'presensi') {
                refreshReportData();
            }
        });
    });

    function loadFilterDropdowns() {
        // Load Jurusan
        $.get('/api/jurusan?per_page=500', function(res) {
            const select = $('#filter-jurusan');
            res.data.data.forEach(item => {
                select.append(`<option value="${item.id}">${item.nama}</option>`);
            });
        });

        // Load Mata Kuliah
        const urlMk = currentUser.role === 'dosen' ? `/api/mata-kuliah?dosen_id=${currentUser.dosen?.id || 0}&per_page=500` : '/api/mata-kuliah?per_page=500';
        $.get(urlMk, function(res) {
            const select = $('#filter-mata-kuliah');
            res.data.data.forEach(item => {
                select.append(`<option value="${item.id}">${item.nama} (${item.kode})</option>`);
            });
        });
    }

    function switchReportTab(tabType) {
        currentTab = tabType;
        
        // Destroy existing table
        // Hapus DataTable lama dengan aman
    if ($.fn.DataTable.isDataTable('#laporan-table')) {
        $('#laporan-table').DataTable().clear().destroy();
    }

    // Kosongkan header dan body lama
    $('#laporan-table tbody').empty();
    $('#laporan-table thead').empty();

        // Update active tab styles
        $('.report-tab').removeClass('border-indigo-600 text-indigo-600').addClass('border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300');
        $(`.report-tab[data-type="${tabType}"]`).removeClass('border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300').addClass('border-indigo-600 text-indigo-600 font-bold');

        // Set dynamic headers
        $('#laporan-table thead').html(headersConfig[tabType]);

        // Hide or Show components based on tab type
        if (tabType === 'presensi') {
            $('#report-title').text('Laporan Rekapitulasi Presensi');
            $('#report-subtitle').text('Pantau dan cetak rekap kehadiran mahasiswa secara komprehensif.');
            $('#filters-section').removeClass('hidden');
            $('#statistics-section').removeClass('hidden');
            $('#print-meta-section').removeClass('hidden');
        } else {
            const titles = {
                mahasiswa: 'Laporan Master Data Mahasiswa',
                dosen: 'Laporan Master Data Dosen',
                matkul: 'Laporan Master Data Mata Kuliah',
                gedung: 'Laporan Master Data Gedung / Ruangan',
                jurusan: 'Laporan Master Data Jurusan'
            };
            const subtitles = {
                mahasiswa: 'Unduh dan cetak seluruh daftar data mahasiswa terdaftar.',
                dosen: 'Unduh dan cetak seluruh daftar dosen institusi.',
                matkul: 'Unduh dan cetak katalog lengkap mata kuliah beserta dosen pengampu.',
                gedung: 'Unduh dan cetak kapasitas gedung, lantai, dan ruangan kelas.',
                jurusan: 'Unduh dan cetak daftar program studi yang tersedia.'
            };
            $('#report-title').text(titles[tabType]);
            $('#report-subtitle').text(subtitles[tabType]);
            $('#filters-section').addClass('hidden');
            $('#statistics-section').addClass('hidden');
            $('#print-meta-section').addClass('hidden');
        }

        // Initialize DataTable
        laporanTable = $('#laporan-table').DataTable({
            dom: 't',
            pageLength: 1000,
            columns: columnsConfig[tabType],
            order: tabType === 'presensi' ? [[9, 'asc']] : [[0, 'asc']],
            language: {
                emptyTable: `<div class='text-center py-8 text-slate-400 italic text-xs'>Tidak ada data laporan.</div>`
            }
        });

        // Load data
        loadTabData(tabType);
    }

    function loadTabData(tabType) {
        if (tabType === 'presensi') {
            refreshReportData();
            return;
        }

        const urls = {
            mahasiswa: '/api/mahasiswa?per_page=1000&include=user,jurusan',
            dosen: '/api/dosen?per_page=1000&include=user,jurusan',
            matkul: '/api/mata-kuliah?per_page=1000&include=dosen.user,jurusan',
            gedung: '/api/gedung?per_page=1000',
            jurusan: '/api/jurusan?per_page=1000'
        };

        $.ajax({
            url: urls[tabType],
            type: 'GET',
            success: function(res) {
                activeData = res.data.data;
                laporanTable.clear();
                laporanTable.rows.add(activeData);
                laporanTable.draw();
            },
            error: function() {
                Swal.fire('Gagal!', 'Tidak dapat mengambil data master dari server.', 'error');
            }
        });
    }

    function refreshReportData() {
        const ta = $('#filter-tahun-ajaran').val();
        const mkId = $('#filter-mata-kuliah').val();
        const jurId = $('#filter-jurusan').val();
        const sd = $('#filter-start-date').val();
        const ed = $('#filter-end-date').val();

        // Update Metadata for Print
        $('#print-meta-ta').text(ta || 'Semua Tahun Ajaran');
        $('#print-meta-mk').text($('#filter-mata-kuliah option:selected').text() || 'Semua Mata Kuliah');
        $('#print-meta-jurusan').text($('#filter-jurusan option:selected').text() || 'Semua Jurusan');
        $('#print-meta-tgl').text(sd && ed ? `${sd} s/d ${ed}` : (sd ? `>= ${sd}` : (ed ? `<= ${ed}` : 'Semua Tanggal')));

        // Prepare Query String
        let queryParams = [];
        if (ta) queryParams.push(`tahun_ajaran=${encodeURIComponent(ta)}`);
        if (mkId) queryParams.push(`mata_kuliah_id=${mkId}`);
        if (jurId) queryParams.push(`jurusan_id=${jurId}`);
        if (sd) queryParams.push(`start_date=${sd}`);
        if (ed) queryParams.push(`end_date=${ed}`);
        
        const url = '/api/laporan/presensi?' + queryParams.join('&');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                const summary = res.data.summary;
                activeData = res.data.details;

                // Update Summary statistics cards
                $('#stat-total-students').text(summary.total_students);
                $('#stat-avg-attendance').text(`${summary.avg_attendance}%`);
                $('#stat-excused').text(`${summary.total_sakit} / ${summary.total_izin}`);
                $('#stat-alpha').text(summary.total_alpha);

                // Update Datatable
                laporanTable.clear();
                laporanTable.rows.add(activeData);
                laporanTable.draw();
            },
            error: function(err) {
                console.error(err);
                Swal.fire('Gagal!', 'Tidak dapat memuat data laporan dari server.', 'error');
            }
        });
    }

    function exportToExcel() {
        if (!activeData || activeData.length === 0) {
            Swal.fire('Info', 'Tidak ada data untuk diekspor.', 'info');
            return;
        }

        let wsData = [];
        let sheetName = "Laporan";

        if (currentTab === 'presensi') {
            sheetName = "Rekap Presensi";
            wsData = activeData.map(row => ({
                'NIM': row.nim,
                'Nama Mahasiswa': row.nama_mahasiswa,
                'Jurusan': row.jurusan,
                'Mata Kuliah': row.mata_kuliah,
                'Kode MK': row.kode_mk,
                'Total Sesi': row.total_sesi,
                'Hadir': row.hadir,
                'Izin': row.izin,
                'Sakit': row.sakit,
                'Alpha': row.alpha,
                'Persentase Kehadiran': `${row.persentase}%`
            }));
        } else if (currentTab === 'mahasiswa') {
            sheetName = "Data Mahasiswa";
            wsData = activeData.map(row => ({
                'NIM': row.nim,
                'Nama Mahasiswa': row.user?.nama || '-',
                'Email': row.user?.email || '-',
                'Jurusan': row.jurusan?.nama || '-',
                'Angkatan': row.angkatan
            }));
        } else if (currentTab === 'dosen') {
            sheetName = "Data Dosen";
            wsData = activeData.map(row => ({
                'NIDN': row.nidn,
                'Nama Dosen': row.user?.nama || '-',
                'Email': row.user?.email || '-',
                'Jurusan': row.jurusan?.nama || '-'
            }));
        } else if (currentTab === 'matkul') {
            sheetName = "Mata Kuliah";
            wsData = activeData.map(row => ({
                'Kode MK': row.kode,
                'Nama Mata Kuliah': row.nama,
                'SKS': row.sks,
                'Semester': row.semester,
                'Dosen Pengampu': row.dosen?.user?.nama || '-'
            }));
        } else if (currentTab === 'gedung') {
            sheetName = "Data Gedung";
            wsData = activeData.map(row => ({
                'Nama Gedung': row.gedung,
                'Lantai': row.lantai,
                'Ruangan': row.ruangan
            }));
        } else if (currentTab === 'jurusan') {
            sheetName = "Data Jurusan";
            wsData = activeData.map(row => ({
                'Kode Jurusan': row.kode,
                'Nama Jurusan': row.nama
            }));
        }

        const ws = XLSX.utils.json_to_sheet(wsData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
        XLSX.writeFile(wb, `Laporan_${sheetName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.xlsx`);
    }
</script>
@endpush
