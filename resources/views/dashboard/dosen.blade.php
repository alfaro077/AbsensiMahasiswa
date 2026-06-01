@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white shadow-lg">
        <h1 class="text-3xl font-bold">Selamat Datang, <span id="welcome-name">Dosen</span>!</h1>
        <p class="mt-2 text-indigo-100 italic">Kelola sesi perkuliahan dan pantau kehadiran mahasiswa Anda dengan mudah.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quick Stats -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Total Mata Kuliah</h3>
            <p class="text-3xl font-bold text-slate-800 mt-2" id="stat-matkul">-</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Sesi Aktif Hari Ini</h3>
            <p class="text-3xl font-bold text-indigo-600 mt-2" id="stat-sesi">-</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Rata-rata Kehadiran</h3>
            <p class="text-3xl font-bold text-emerald-600 mt-2">85%</p>
        </div>
    </div>

    <!-- Sesi Kuliah Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="text-xl font-bold text-slate-800">Daftar Sesi Kuliah Terbaru</h2>
            <button onclick="openSesiModal()" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-all flex items-center justify-center gap-2 text-sm shadow-md active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Buat Sesi Baru
            </button>
        </div>
        <div class="p-0 sm:p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="sesi-table" class="w-full text-left text-sm border-collapse">
                    <thead class="bg-indigo-50/50 text-indigo-700 font-bold border-b border-indigo-100 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4">Mata Kuliah</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Waktu</th>
                            <th class="p-4">Lokasi</th>
                            <th class="p-4">Topik</th>
                            <th class="p-4">Kode Absen</th>
                            <th class="p-4 text-center">Hadir</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Sesi -->
<div id="sesi-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-bold text-slate-800">Buat Sesi Kuliah Baru</h3>
            <button onclick="closeSesiModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="sesi-form" class="p-5 space-y-4">
            <!-- Step 1: Pilih Mata Kuliah -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Mata Kuliah</label>
                <select id="mata_kuliah_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500"></select>
            </div>

            <!-- Step 2: Pilih Jadwal (dari Admin) -->
            <div id="jadwal-select-container" class="hidden">
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Pilih Jadwal
                    <span class="text-xs text-slate-400 font-normal">(ditentukan admin)</span>
                </label>
                <select id="jadwal_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Jadwal --</option>
                </select>
                <p id="no-jadwal-msg" class="hidden text-xs text-amber-600 mt-1 font-medium">
                    ⚠️ Belum ada jadwal untuk mata kuliah ini. Hubungi admin untuk menambahkan jadwal.
                </p>
            </div>

            <!-- Auto-filled info card -->
            <div id="jadwal-info-card" class="hidden bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 space-y-2">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">
                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Info Jadwal Terpilih
                </p>
                <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-sm">
                    <div><span class="text-slate-500">Hari:</span> <span id="info-hari" class="font-semibold text-slate-800">-</span></div>
                    <div><span class="text-slate-500">Waktu:</span> <span id="info-waktu" class="font-semibold text-slate-800">-</span></div>
                    <div><span class="text-slate-500">Gedung:</span> <span id="info-gedung" class="font-semibold text-slate-800">-</span></div>
                    <div><span class="text-slate-500">Ruangan:</span> <span id="info-lantai-ruangan" class="font-semibold text-slate-800">-</span></div>
                </div>
            </div>

            <!-- Hidden fields populated by jadwal selection -->
            <input type="hidden" id="gedung" value="">
            <input type="hidden" id="lantai" value="">
            <input type="hidden" id="ruangan" value="">

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Sesi</label>
                <input type="date" id="tanggal" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Jam Override (pre-filled from jadwal, editable) -->
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label>
                    <input type="time" id="jam_mulai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label>
                    <input type="time" id="jam_selesai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Topik -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Topik Perkuliahan</label>
                <input type="text" id="topik" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Contoh: Pengenalan Laravel">
            </div>

            <!-- Kode Absensi -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kode Absensi (Alphanumeric)</label>
                <div class="flex gap-2">
                    <input type="text" id="kode_unik" required maxlength="6" class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold uppercase" placeholder="KODE12">
                    <button type="button" onclick="generateRandomCode()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-lg text-xs font-bold transition-all">GENERATE</button>
                </div>
            </div>

            <!-- Error Container -->
            <div id="sesi-error-container" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm text-red-700 font-medium" id="sesi-error-msg"></p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeSesiModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">Batal</button>
                <button type="submit" id="btn-submit-sesi" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md transition-all">Simpan Sesi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code -->
<div id="qr-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-md transition-opacity">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform transition-all">
        <div class="bg-indigo-600 p-6 text-white text-center relative">
            <h3 class="text-xl font-black uppercase italic tracking-tighter">Scan QR Code</h3>
            <p class="text-indigo-100 text-xs mt-1" id="qr-modal-topik"></p>
            <button onclick="closeQrModal()" class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-8 flex flex-col items-center">
            <div id="qrcode" class="bg-white p-4 rounded-2xl border-4 border-indigo-50 shadow-inner"></div>
            <div class="mt-6 text-center">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">KODE UNIK</span>
                <div class="text-3xl font-black text-indigo-700 tracking-[0.3em] mt-1" id="qr-modal-kode">------</div>
            </div>
        </div>
        <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
            <p class="text-[10px] text-slate-400 font-medium italic">Tampilkan layar ini kepada mahasiswa untuk melakukan absensi</p>
        </div>
    </div>
</div>

<!-- Modal Detail Absen -->
<div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden max-h-[80vh] flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Kehadiran Mahasiswa</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="detail-modal-info"></p>
            </div>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-grow overflow-y-auto p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] sticky top-0 z-10 border-b border-slate-100">
                    <tr>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Waktu Absen</th>
                        <th class="p-4">Metode</th>
                        <th class="p-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody id="detail-student-list" class="divide-y divide-slate-50">
                    <!-- Dynamic -->
                </tbody>
            </table>
            <div id="detail-empty" class="hidden p-12 text-center">
                <div class="inline-flex p-4 rounded-full bg-slate-100 text-slate-300 mb-4">
                   <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <p class="text-slate-400 font-medium">Belum ada mahasiswa yang hadir.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let sesiTable, qrGenerator;
    let editingSesiId = null;
    let editingSesiActive = 1;
    const user = JSON.parse(localStorage.getItem('user'));
    // Cache jadwal per mata kuliah
    let jadwalCache = {};

    $(document).ready(function() {
        $('#welcome-name').text(user.nama);

        // Initialize QR Code generator
        qrGenerator = new QRCode(document.getElementById("qrcode"), {
            width: 200,
            height: 200,
            colorDark : "#4338ca",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Populate Mata Kuliah Select
        const dosenId = user.dosen?.id || 0;
        $.get('/api/mata-kuliah?per_page=100&dosen_id=' + dosenId, function(res) {
            const matkuls = res.data.data;
            $('#stat-matkul').text(matkuls.length);
            $('#mata_kuliah_id').empty().append('<option value="">-- Pilih Mata Kuliah --</option>');
            matkuls.forEach(m => {
                $('#mata_kuliah_id').append(`<option value="${m.id}">${m.nama} (${m.kode})</option>`);
            });
        });

        // When mata kuliah changes → load jadwal for it
        $('#mata_kuliah_id').on('change', function() {
            const mkId = $(this).val();
            $('#jadwal_id').val('');
            $('#jadwal-info-card').addClass('hidden');
            $('#sesi-error-container').addClass('hidden');
            // Reset hidden fields
            $('#gedung').val('');
            $('#lantai').val('');
            $('#ruangan').val('');

            if (!mkId) {
                $('#jadwal-select-container').addClass('hidden');
                return;
            }

            $('#jadwal-select-container').removeClass('hidden');
            loadJadwalForMatkul(mkId);
        });

        // When jadwal changes → auto-fill fields
        $('#jadwal_id').on('change', function() {
            const jadwalId = $(this).val();
            $('#sesi-error-container').addClass('hidden');

            if (!jadwalId) {
                $('#jadwal-info-card').addClass('hidden');
                $('#gedung').val('');
                $('#lantai').val('');
                $('#ruangan').val('');
                $('#jam_mulai').val('');
                $('#jam_selesai').val('');
                return;
            }

            // Find jadwal data from cache
            const mkId = $('#mata_kuliah_id').val();
            const jadwalList = jadwalCache[mkId] || [];
            const jadwal = jadwalList.find(j => j.id == jadwalId);

            if (jadwal) {
                // Auto-fill time
                $('#jam_mulai').val(jadwal.jam_mulai?.substring(0, 5));
                $('#jam_selesai').val(jadwal.jam_selesai?.substring(0, 5));

                // Auto-fill location (hidden fields for submit)
                const gedungName = jadwal.gedung?.nama || jadwal.gedung || '-';
                $('#gedung').val(gedungName);
                $('#lantai').val(jadwal.lantai);
                $('#ruangan').val(jadwal.ruangan);

                // Show info card
                $('#info-hari').text(jadwal.hari);
                $('#info-waktu').text(`${jadwal.jam_mulai?.substring(0, 5)} - ${jadwal.jam_selesai?.substring(0, 5)}`);
                $('#info-gedung').text(gedungName);
                $('#info-lantai-ruangan').text(`Lt. ${jadwal.lantai}, ${jadwal.ruangan}`);
                $('#jadwal-info-card').removeClass('hidden');
            }
        });

        // Initialize Sesi Table
        sesiTable = $('#sesi-table').DataTable({
            ajax: {
                url: '/api/sesi-kuliah?include=mataKuliah&sort_by=id&sort_dir=desc&dosen_id=' + (user.dosen?.id || 0),
                dataSrc: function(json) {
                    const activeSesi = json.data.data.filter(s => s.is_active).length;
                    $('#stat-sesi').text(activeSesi);
                    return json.data.data; 
                }
            },
            columns: [
                { 
                    data: 'mata_kuliah.nama',
                    className: 'p-4 font-semibold text-slate-800'
                },
                { 
                    data: 'tanggal',
                    className: 'p-4 text-slate-600',
                    render: val => `<div class="flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>${val}</div>`
                },
                { 
                    data: null, 
                    className: 'p-4 text-slate-500',
                    render: row => `<div class="flex items-center gap-2"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${row.jam_mulai} - ${row.jam_selesai}</div>` 
                },
                { 
                    data: null, 
                    className: 'p-4 text-slate-600',
                    render: row => row.gedung ? `<div class="flex flex-col"><span class="font-semibold text-slate-700">${row.gedung}</span><span class="text-[10px] text-slate-400 font-medium">${row.lantai}, ${row.ruangan}</span></div>` : '<span class="text-slate-300 italic">-</span>'
                },
                { 
                    data: 'topik',
                    className: 'p-4 text-slate-600 italic' 
                },
                { 
                    data: 'kode_unik',
                    className: 'p-4 text-center',
                    render: (val, type, row) => `<button onclick="openQrModal('${val}', '${row.topik}')" class="bg-indigo-50 hover:bg-indigo-100 px-2 sm:px-3 py-1.5 rounded-lg font-mono font-bold text-indigo-700 border border-indigo-100 shadow-sm transition-all hover:scale-105 active:scale-95 group flex flex-col items-center gap-1 mx-auto">
                        <span class="text-xs sm:text-sm">${val}</span>
                        <span class="text-[7px] sm:text-[8px] text-indigo-300 group-hover:text-indigo-500 font-sans uppercase tracking-[0.1em] sm:tracking-widest">Klik QR</span>
                    </button>`
                },
                { 
                    data: 'presensi_count', 
                    className: 'p-4 text-center',
                    render: val => `<div class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-indigo-200 shadow-sm"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h11v-1a6.97 6.97 0 00-1.5-4.33A5 5 0 016 11z"></path></svg>${val}</div>`
                },
                { 
                    data: 'is_active',
                    className: 'p-4 text-center',
                    render: val => val ? '<span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase tracking-wider border border-emerald-200 shadow-sm">Aktif</span>' : '<span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase tracking-wider border border-slate-200">Selesai</span>'
                },
                {
                    data: null,
                    className: 'p-4 text-right',
                    render: row => `
                        <div class="flex justify-end gap-2">
                            <button onclick="viewAbsen(${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-indigo-600 hover:text-white hover:bg-indigo-600 px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm border border-indigo-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span class="hidden xs:inline uppercase">Detail</span>
                            </button>
                            <button onclick="editSesi(${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-amber-600 hover:text-white hover:bg-amber-600 px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm border border-amber-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                <span class="hidden xs:inline uppercase">Ubah</span>
                            </button>
                            ${row.is_active ? `
                            <button onclick="toggleActive(${row.id}, false)" class="inline-flex items-center gap-1 sm:gap-1.5 text-red-600 hover:text-white hover:bg-red-600 px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm border border-red-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="hidden xs:inline uppercase">Tutup</span>
                            </button>` : ''}
                        </div>
                    `
                }
            ],
            language: { 
                search: "",
                searchPlaceholder: "Cari sesi...",
                lengthMenu: "_MENU_",
            },
            drawCallback: function() {
                $('.dataTables_paginate').addClass('flex justify-end gap-1 mt-4 px-4 pb-4');
                $('.dataTables_info').addClass('text-xs text-slate-400 px-4 pb-4');
            },
            order: [[1, 'desc']]
        });

        // Handle Form
        $('#sesi-form').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btn-submit-sesi');
            btn.prop('disabled', true).text('Menyimpan...');
            $('#sesi-error-container').addClass('hidden');

            const isEditing = editingSesiId !== null;
            const url = isEditing ? `/api/sesi-kuliah/${editingSesiId}` : '/api/sesi-kuliah';
            const method = isEditing ? 'PUT' : 'POST';

            const payload = {
                mata_kuliah_id: $('#mata_kuliah_id').val(),
                tanggal: $('#tanggal').val(),
                jam_mulai: $('#jam_mulai').val(),
                jam_selesai: $('#jam_selesai').val(),
                topik: $('#topik').val() || 'Pelajaran Umum',
                gedung: $('#gedung').val(),
                lantai: $('#lantai').val(),
                ruangan: $('#ruangan').val(),
                kode_unik: $('#kode_unik').val().toUpperCase(),
                qr_code: $('#kode_unik').val().toUpperCase(),
                is_active: isEditing ? editingSesiActive : 1
            };

            $.ajax({
                url: url,
                type: method,
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(res) {
                    closeSesiModal();
                    sesiTable.ajax.reload();
                    
                    if (isEditing) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sesi Diperbarui',
                            text: 'Detail sesi kuliah telah berhasil diperbarui.',
                            confirmButtonColor: '#4f46e5'
                        }).then(() => {
                            if (editingSesiActive) {
                                openQrModal(payload.kode_unik, payload.topik);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sesi Dibuat',
                            text: 'Silakan tampilkan QR Code kepada mahasiswa.',
                            confirmButtonColor: '#4f46e5'
                        }).then(() => {
                            openQrModal(payload.kode_unik, payload.topik);
                        });
                    }
                },
                error: function(err) {
                    if (err.status === 422 && err.responseJSON?.errors) {
                        const errors = err.responseJSON.errors;
                        const errorMsg = typeof errors === 'string' ? errors : Object.values(errors).flat().join('\n');
                        $('#sesi-error-msg').text(errorMsg);
                        $('#sesi-error-container').removeClass('hidden');
                    } else {
                        Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error');
                    }
                },
                complete: () => btn.prop('disabled', false).text(isEditing ? 'Perbarui Sesi' : 'Simpan Sesi')
            });
        });
    });

    // Load jadwal options for a specific mata kuliah
    function loadJadwalForMatkul(mkId) {
        const select = $('#jadwal_id');
        select.empty().append('<option value="">Memuat jadwal...</option>');
        $('#no-jadwal-msg').addClass('hidden');

        $.get(`/api/jadwal-mata-kuliah?mata_kuliah_id=${mkId}`, function(res) {
            const jadwalList = res.data?.data || res.data || [];
            jadwalCache[mkId] = jadwalList;

            select.empty().append('<option value="">-- Pilih Jadwal --</option>');

            if (jadwalList.length === 0) {
                $('#no-jadwal-msg').removeClass('hidden');
                select.prop('required', false);
            } else {
                $('#no-jadwal-msg').addClass('hidden');
                select.prop('required', true);
                jadwalList.forEach(j => {
                    const gedungName = j.gedung?.nama || j.gedung || '-';
                    const label = `${j.hari}, ${j.jam_mulai?.substring(0,5)}-${j.jam_selesai?.substring(0,5)} | ${gedungName} Lt.${j.lantai} R.${j.ruangan}`;
                    select.append(`<option value="${j.id}">${label}</option>`);
                });
            }
        }).fail(function() {
            select.empty().append('<option value="">Gagal memuat jadwal</option>');
        });
    }

    function generateRandomCode() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let result = '';
        for (let i = 0; i < 6; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        $('#kode_unik').val(result);
    }

    function openSesiModal() {
        editingSesiId = null;
        editingSesiActive = 1;
        $('#sesi-error-container').addClass('hidden');
        $('#sesi-modal h3').text('Buat Sesi Kuliah Baru');
        $('#btn-submit-sesi').text('Simpan Sesi');

        $('#sesi-form')[0].reset();
        $('#tanggal').val(new Date().toISOString().split('T')[0]);
        generateRandomCode();
        $('#jadwal-select-container').addClass('hidden');
        $('#jadwal-info-card').addClass('hidden');
        $('#no-jadwal-msg').addClass('hidden');
        $('#sesi-modal').removeClass('hidden').addClass('flex');
    }

    function editSesi(id) {
        editingSesiId = id;
        $('#sesi-error-container').addClass('hidden');
        $('#sesi-modal h3').text('Ubah Sesi Kuliah');
        $('#btn-submit-sesi').text('Perbarui Sesi');

        $.get(`/api/sesi-kuliah/${id}?include=mataKuliah`, function(res) {
            const sesi = res.data;
            editingSesiActive = sesi.is_active ? 1 : 0;

            $('#mata_kuliah_id').val(sesi.mata_kuliah_id);
            $('#tanggal').val(sesi.tanggal ? sesi.tanggal.split('T')[0] : '');
            $('#jam_mulai').val(sesi.jam_mulai ? sesi.jam_mulai.substring(0, 5) : '');
            $('#jam_selesai').val(sesi.jam_selesai ? sesi.jam_selesai.substring(0, 5) : '');
            $('#topik').val(sesi.topik);
            $('#kode_unik').val(sesi.kode_unik);
            
            $('#gedung').val(sesi.gedung);
            $('#lantai').val(sesi.lantai);
            $('#ruangan').val(sesi.ruangan);

            $('#jadwal-select-container').removeClass('hidden');
            
            const select = $('#jadwal_id');
            select.empty().append('<option value="">Memuat jadwal...</option>');
            $('#no-jadwal-msg').addClass('hidden');

            $.get(`/api/jadwal-mata-kuliah?mata_kuliah_id=${sesi.mata_kuliah_id}`, function(jRes) {
                const jadwalList = jRes.data?.data || jRes.data || [];
                jadwalCache[sesi.mata_kuliah_id] = jadwalList;

                select.empty().append('<option value="">-- Pilih Jadwal --</option>');

                if (jadwalList.length === 0) {
                    $('#no-jadwal-msg').removeClass('hidden');
                    select.prop('required', false);
                } else {
                    $('#no-jadwal-msg').addClass('hidden');
                    select.prop('required', true);
                    
                    let matchedJadwalId = "";
                    jadwalList.forEach(j => {
                        const gedungName = j.gedung?.nama || j.gedung || '-';
                        const label = `${j.hari}, ${j.jam_mulai?.substring(0,5)}-${j.jam_selesai?.substring(0,5)} | ${gedungName} Lt.${j.lantai} R.${j.ruangan}`;
                        select.append(`<option value="${j.id}">${label}</option>`);

                        if (
                            j.lantai == sesi.lantai &&
                            j.ruangan == sesi.ruangan &&
                            (j.gedung?.nama == sesi.gedung || j.gedung == sesi.gedung)
                        ) {
                            matchedJadwalId = j.id;
                        }
                    });

                    if (matchedJadwalId) {
                        select.val(matchedJadwalId);
                        const matched = jadwalList.find(j => j.id == matchedJadwalId);
                        $('#info-hari').text(matched.hari);
                        const gedungName = matched.gedung?.nama || matched.gedung || '-';
                        $('#info-waktu').text(`${matched.jam_mulai?.substring(0, 5)} - ${matched.jam_selesai?.substring(0, 5)}`);
                        $('#info-gedung').text(gedungName);
                        $('#info-lantai-ruangan').text(`Lt. ${matched.lantai}, R. ${matched.ruangan}`);
                        $('#jadwal-info-card').removeClass('hidden');
                    } else {
                        $('#jadwal-info-card').addClass('hidden');
                    }
                }
            });

            $('#sesi-modal').removeClass('hidden').addClass('flex');
        });
    }

    function closeSesiModal() {
        $('#sesi-modal').addClass('hidden').removeClass('flex');
    }

    function openQrModal(code, topik) {
        $('#qr-modal-topik').text(topik);
        $('#qr-modal-kode').text(code);
        qrGenerator.clear();
        qrGenerator.makeCode(code);
        $('#qr-modal').removeClass('hidden').addClass('flex');
    }

    function closeQrModal() {
        $('#qr-modal').addClass('hidden').removeClass('flex');
    }

    function viewAbsen(id) {
        $('#detail-modal-info').html('<span class="animate-pulse">Memuat data mahasiswa...</span>');
        $('#detail-student-list').empty();
        $('#detail-empty').addClass('hidden');
        $('#detail-modal').removeClass('hidden').addClass('flex');

        $.get(`/api/sesi-kuliah/${id}?include=mataKuliah.mahasiswa.user,presensi.mahasiswa.user`, function(res) {
            const data = res.data;
            const enrolled = data.mata_kuliah?.mahasiswa || [];
            const presensiMap = {};
            
            // Map presensi data for easy lookup
            if (data.presensi) {
                data.presensi.forEach(p => {
                    presensiMap[p.mahasiswa_id] = p;
                });
            }

            const presentCount = Object.keys(presensiMap).length;
            const totalCount = enrolled.length;

            $('#detail-modal-info').html(`
                <div class="flex items-center gap-2 mt-1">
                    <span class="font-bold text-slate-800">${data.mata_kuliah.nama}</span>
                    <span class="text-slate-300">|</span>
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-bold">
                        KEHADIRAN: ${presentCount} / ${totalCount}
                    </span>
                </div>
            `);
            
            if (enrolled.length === 0) {
                $('#detail-empty').removeClass('hidden').html(`
                    <div class="inline-flex p-4 rounded-full bg-slate-100 text-slate-300 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.268 17c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <p class="text-slate-400 font-medium">Tidak ada mahasiswa yang terdaftar di mata kuliah ini.</p>
                `);
            } else {
                enrolled.forEach(m => {
                    const studentName = m.user?.nama || 'N/A';
                    const nim = m.nim || '';
                    const p = presensiMap[m.id];
                    
                    let timeStr = '-', methodStr = '-', statusHtml = '';

                    if (p) {
                        timeStr = new Date(p.waktu_absen).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
                        methodStr = p.metode === 'qr' ? '📷 QR SCAN' : (p.metode === 'kode_unik' ? '⌨️ MANUAL' : '📝 INPUT DOSEN');
                    } else {
                        methodStr = '-';
                    }

                    const selectId = `status-select-${m.id}`;
                    const ketId = `ket-input-${m.id}`;
                    const currentStatus = p ? p.status : '';
                    const isAttended = !!p;
                    const presensiId = p ? p.id : '';
                    
                    let selectColorClass = 'bg-slate-50 text-slate-500 border-slate-200';
                    if (currentStatus === 'hadir') selectColorClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    else if (currentStatus === 'izin') selectColorClass = 'bg-amber-50 text-amber-700 border-amber-200';
                    else if (currentStatus === 'sakit') selectColorClass = 'bg-sky-50 text-sky-700 border-sky-200';
                    else if (currentStatus === 'alpha') selectColorClass = 'bg-rose-50 text-rose-700 border-rose-200';

                    if (currentStatus === 'pending_izin' || currentStatus === 'pending_sakit') {
                        const leaveType = currentStatus === 'pending_izin' ? 'Izin' : 'Sakit';
                        const targetStatus = currentStatus === 'pending_izin' ? 'izin' : 'sakit';
                        const reason = p.keterangan || '(Tanpa alasan)';
                        
                        statusHtml = `
                            <div class="flex flex-col items-end gap-1 bg-amber-50/50 p-2 rounded-xl border border-amber-200 shadow-sm max-w-[280px] ml-auto">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-amber-700">⌛ Pengajuan ${leaveType}</span>
                                </div>
                                <p class="text-[10px] text-slate-600 font-medium italic text-right max-h-12 overflow-y-auto break-all my-1 px-1" title="${reason}">
                                    "${reason}"
                                </p>
                                <div class="flex gap-1.5 mt-0.5">
                                    <button 
                                        data-reason="${p.keterangan || ''}" 
                                        data-metode="${p.metode}"
                                        onclick="approvePending(${presensiId}, '${targetStatus}', ${data.id}, ${m.id}, this.dataset.metode, this.dataset.reason)" 
                                        class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[9px] font-bold uppercase transition-all shadow-sm active:scale-95 flex items-center gap-1">
                                        ✔ Setujui
                                    </button>
                                    <button onclick="rejectPending(${presensiId}, ${data.id})" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[9px] font-bold uppercase transition-all shadow-sm active:scale-95 flex items-center gap-1">
                                        ✖ Tolak
                                    </button>
                                </div>
                            </div>
                        `;
                    } else {
                        statusHtml = `
                            <div class="flex items-center justify-end gap-2">
                                <input type="text" id="${ketId}" value="${p && p.keterangan ? p.keterangan : ''}" placeholder="Alasan / Keterangan..." 
                                    data-presensi-id="${presensiId}"
                                    onblur="if(this.dataset.presensiId) updateStudentStatus(${data.id}, ${m.id}, this.dataset.presensiId, $('#${selectId}').val(), this.value)"
                                    class="text-xs px-2.5 py-1.5 rounded-lg border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500 w-24 sm:w-36 bg-slate-50/50 focus:bg-white transition-all shadow-sm">
                                <select id="${selectId}" onchange="updateStudentStatus(${data.id}, ${m.id}, '${presensiId}', this.value, $('#${ketId}').val())" 
                                    class="text-xs px-2.5 py-1.5 rounded-lg border font-bold uppercase tracking-wider shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer ${selectColorClass}">
                                    <option value="" ${currentStatus === '' ? 'selected' : ''} class="text-slate-500 bg-white">BELUM HADIR</option>
                                    <option value="hadir" ${currentStatus === 'hadir' ? 'selected' : ''} class="text-emerald-700 bg-white">HADIR</option>
                                    <option value="izin" ${currentStatus === 'izin' ? 'selected' : ''} class="text-amber-700 bg-white">IZIN</option>
                                    <option value="sakit" ${currentStatus === 'sakit' ? 'selected' : ''} class="text-sky-700 bg-white">SAKIT</option>
                                    <option value="alpha" ${currentStatus === 'alpha' ? 'selected' : ''} class="text-rose-700 bg-white">ALPHA</option>
                                </select>
                            </div>
                        `;
                    }
                    
                    $('#detail-student-list').append(`
                        <tr class="hover:bg-slate-50 transition-colors ${!p ? 'bg-slate-50/30' : ''}">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full ${p ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400'} flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                                        ${studentName.charAt(0)}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold ${p ? 'text-slate-800' : 'text-slate-500'}">${studentName}</span>
                                        <span class="text-[10px] text-slate-400 font-mono uppercase tracking-widest">${nim}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 font-medium text-slate-600">${timeStr}</td>
                            <td class="p-4 text-center">
                                <span class="text-[10px] ${p ? 'bg-indigo-50 text-indigo-500 border-indigo-100' : 'bg-slate-100 text-slate-300 border-slate-200'} px-2 py-1 rounded font-bold uppercase tracking-wider border">
                                    ${methodStr}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                ${statusHtml}
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    function closeDetailModal() {
        $('#detail-modal').addClass('hidden').removeClass('flex');
    }

    function toggleActive(id, status) {
        Swal.fire({
            title: status ? 'Aktifkan Sesi?' : 'Tutup Sesi Kuliah?',
            text: status ? 'Mahasiswa dapat kembali melakukan absensi.' : 'Setelah ditutup, mahasiswa tidak bisa lagi melakukan absensi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: status ? '#10b981' : '#ef4444',
            confirmButtonText: status ? 'Ya, Aktifkan!' : 'Ya, Tutup Sesi!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/sesi-kuliah/${id}`,
                    type: 'PUT',
                    data: JSON.stringify({ is_active: status ? 1 : 0 }),
                    contentType: 'application/json',
                    success: function(res) {
                        sesiTable.ajax.reload();
                        showNotification('Berhasil', status ? 'Sesi diaktifkan' : 'Sesi telah ditutup');
                    },
                    error: (err) => Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error')
                });
            }
        });
    }

    function updateStudentStatus(sesiId, mahasiswaId, presensiId, status, keterangan) {
        // Jika status kosong (BELUM HADIR), dan presensiId ada, hapus record presensinya
        if (!status) {
            if (presensiId) {
                $.ajax({
                    url: `/api/presensi/${presensiId}`,
                    type: 'DELETE',
                    success: function() {
                        showNotification('Berhasil', 'Status kehadiran berhasil dibatalkan', 'success');
                        viewAbsen(sesiId); // Refresh modal detail
                        sesiTable.ajax.reload(null, false); // Reload tabel utama tanpa reset paging
                    },
                    error: (err) => Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error')
                });
            }
            return;
        }

        const payload = {
            sesi_id: sesiId,
            mahasiswa_id: mahasiswaId,
            status: status,
            keterangan: keterangan || '',
            metode: 'manual'
        };

        if (presensiId) {
            // Update record presensi yang sudah ada
            $.ajax({
                url: `/api/presensi/${presensiId}`,
                type: 'PUT',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function() {
                    showNotification('Berhasil', 'Status kehadiran berhasil diperbarui', 'success');
                    viewAbsen(sesiId);
                    sesiTable.ajax.reload(null, false);
                },
                error: (err) => Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error')
            });
        } else {
            // Buat record presensi baru
            $.ajax({
                url: `/api/presensi`,
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function() {
                    showNotification('Berhasil', 'Status kehadiran berhasil dicatat', 'success');
                    viewAbsen(sesiId);
                    sesiTable.ajax.reload(null, false);
                },
                error: (err) => Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error')
            });
        }
    }

    function approvePending(presensiId, targetStatus, sesiId, mahasiswaId, metode, keterangan) {
        Swal.fire({
            title: 'Setujui Pengajuan?',
            text: `Apakah Anda yakin ingin menyetujui pengajuan ${targetStatus === 'izin' ? 'Izin' : 'Sakit'} ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const payload = {
                    sesi_id: sesiId,
                    mahasiswa_id: mahasiswaId,
                    metode: metode || 'manual',
                    status: targetStatus,
                    keterangan: keterangan || ''
                };
                
                $.ajax({
                    url: `/api/presensi/${presensiId}`,
                    type: 'PUT',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: function() {
                        showNotification('Berhasil', 'Pengajuan berhasil disetujui', 'success');
                        viewAbsen(sesiId);
                        sesiTable.ajax.reload(null, false);
                    },
                    error: (err) => Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error')
                });
            }
        });
    }

    function rejectPending(presensiId, sesiId) {
        Swal.fire({
            title: 'Tolak Pengajuan?',
            text: 'Apakah Anda yakin ingin menolak pengajuan ini? Status mahasiswa akan dikembalikan menjadi Belum Hadir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/presensi/${presensiId}`,
                    type: 'DELETE',
                    success: function() {
                        showNotification('Berhasil', 'Pengajuan telah ditolak', 'success');
                        viewAbsen(sesiId);
                        sesiTable.ajax.reload(null, false);
                    },
                    error: (err) => Swal.fire('Gagal', err.responseJSON?.message || 'Error', 'error')
                });
            }
        });
    }
</script>
@endpush
