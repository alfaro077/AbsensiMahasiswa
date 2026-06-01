@extends('layouts.app')
@section('title', 'Beranda Mahasiswa')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 sm:p-8 text-white shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div class="max-w-2xl">
            <h1 class="text-2xl sm:text-3xl font-bold">Halo, <span id="welcome-name">Mahasiswa</span>!</h1>
            <p class="mt-2 text-emerald-50 text-sm sm:text-base">Ingat untuk selalu mengisi absensi tepat waktu sesuai jadwal perkuliahan.</p>
        </div>
        <div class="bg-white/20 backdrop-blur-md p-4 rounded-xl border border-white/30 text-center w-full md:w-auto md:min-w-[200px] shadow-inner">
            <p class="text-[10px] uppercase font-black tracking-[0.2em] opacity-70">Waktu Sekarang</p>
            <p class="text-2xl font-mono font-bold mt-1" id="live-clock">00:00:00</p>
        </div>
    </div>

    <!-- Active Sessions Section -->
    <div id="active-sessions-section" class="hidden space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                </span>
                Sesi Perkuliahan Aktif
            </h2>
            <p class="text-xs text-slate-500 font-medium">Klik untuk menyalin kode absensi</p>
        </div>
        <div id="active-sessions-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dynamic Active Sessions -->
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-1 space-y-6 self-start">
            <!-- Input Absensi Card -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Isi Kehadiran
                </h2>
                <form id="absen-form" class="space-y-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kode Unik / Scan QR</label>
                        <div class="flex flex-col xs:flex-row gap-2">
                            <input type="text" id="kode_unik" required class="w-full xs:flex-grow rounded-lg border border-slate-300 px-4 py-3 outline-none focus:ring-2 focus:ring-emerald-500 text-center text-xl font-bold tracking-widest uppercase placeholder:text-slate-300 shadow-sm" placeholder="KODE-123">
                            <button type="button" onclick="openScanner()" class="w-full xs:w-auto bg-slate-100 hover:bg-emerald-50 text-emerald-600 hover:text-emerald-700 p-3.5 rounded-lg border border-slate-200 transition-all group flex items-center justify-center" title="Scan QR Code">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 italic text-center text-balance leading-relaxed">*Dapatkan kode atau scan QR dari Dosen pengampu di kelas.</p>
                    </div>
                    <button type="submit" id="btn-absen" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                        Absen Sekarang
                    </button>
                </form>
            </div>

            <!-- Mata Kuliah Anda Card -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Mata Kuliah Anda
                </h2>
                <div id="enrolled-courses-list" class="space-y-3">
                    <div class="animate-pulse flex space-x-4">
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-slate-200 rounded"></div>
                                <div class="h-4 bg-slate-200 rounded w-5/6"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner Modal -->
        <div id="scanner-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/80 backdrop-blur-md">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden relative">
                <div class="bg-emerald-600 p-4 text-white flex justify-between items-center">
                    <h3 class="font-bold">Scan QR Code Absensi</h3>
                    <button onclick="closeScanner()" class="text-white/50 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div id="reader" class="rounded-2xl overflow-hidden border-4 border-slate-50 bg-slate-100 aspect-square"></div>
                    <p class="mt-4 text-sm text-slate-500 text-center font-medium italic">Arahkan kamera ke QR Code yang ditampilkan Dosen</p>
                </div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 italic flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 not-italic">Riwayat Kehadiran Anda</h2>
                <div class="text-xs text-slate-500">Menampilkan 10 data terakhir</div>
            </div>
            <div class="p-0 sm:p-6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="history-table" class="w-full text-left text-sm border-collapse">
                        <thead class="bg-emerald-50/50 text-emerald-700 font-bold border-b border-emerald-100 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-4">Waktu</th>
                                <th class="p-4">Mata Kuliah</th>
                                <th class="p-4">Topik</th>
                                <th class="p-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let historyTable, html5QrCode;
    const user = JSON.parse(localStorage.getItem('user'));

    $(document).ready(function() {
        $('#welcome-name').text(user.nama);

        // Live Clock
        setInterval(() => {
            const now = new Date();
            $('#live-clock').text(now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }));
        }, 1000);

        // Initialize History Table
        historyTable = $('#history-table').DataTable({
            ajax: {
                url: '/api/presensi?include=sesiKuliah.mataKuliah&mahasiswa_id=' + (user.mahasiswa?.id || 0),
                dataSrc: 'data.data'
            },
            columns: [
                { 
                    data: 'waktu_absen',
                    className: 'p-4 text-slate-600',
                    render: val => val ? `<div class="flex items-center gap-2 font-mono text-xs"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>${new Date(val).toLocaleString('id-ID')}</div>` : '-'
                },
                { 
                    data: 'sesi_kuliah.mata_kuliah.nama', 
                    className: 'p-4',
                    render: (data, type, row) => {
                        const mk = row.sesi_kuliah?.mata_kuliah;
                        return mk ? `
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800">${mk.nama}</span>
                                <span class="text-[10px] text-slate-400 font-mono tracking-wider">${mk.kode}</span>
                            </div>
                        ` : '<span class="text-slate-300 italic text-xs">N/A</span>';
                    }
                },
                { 
                    data: 'sesi_kuliah.topik', 
                    className: 'p-4 text-slate-500 italic',
                    render: val => val ? `<span class="text-xs">"${val}"</span>` : '-'
                },
                { 
                    data: 'status',
                    className: 'p-4 text-center',
                    render: val => {
                        const colors = {
                            'hadir': 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'izin': 'bg-amber-50 text-amber-700 border-amber-100',
                            'sakit': 'bg-sky-50 text-sky-700 border-sky-100',
                            'alpha': 'bg-red-50 text-red-700 border-red-100',
                            'pending_izin': 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse',
                            'pending_sakit': 'bg-amber-50 text-amber-600 border-amber-200 animate-pulse'
                        };
                        const labels = {
                            'hadir': 'hadir',
                            'izin': 'izin',
                            'sakit': 'sakit',
                            'alpha': 'alpha',
                            'pending_izin': '⌛ pending izin',
                            'pending_sakit': '⌛ pending sakit'
                        };
                        const displayVal = labels[val] || val;
                        return `<span class="px-2.5 py-1 ${colors[val] || 'bg-slate-50'} rounded-full text-[10px] font-black uppercase tracking-widest border shadow-sm">${displayVal}</span>`;
                    }
                }
            ],
            dom: 't',
            order: [[0, 'desc']],
            pageLength: 10
        });

        // Load Active Sessions
        loadActiveSessions();
        setInterval(loadActiveSessions, 30000); // Refresh every 30s

        // Load Enrolled Courses
        loadEnrolledCourses();

        // Handle Form Absensi
        $('#absen-form').on('submit', function(e) {
            e.preventDefault();
            submitAbsensi($('#kode_unik').val(), 'kode_unik');
        });
    });

    function openScanner() {
        $('#scanner-modal').removeClass('hidden').addClass('flex');
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
            // Success callback
            $('#kode_unik').val(decodedText);
            closeScanner();
            
            // Auto submit
            Swal.fire({
                title: 'QR Terdeteksi!',
                text: `Kirim absensi untuk kode: ${decodedText}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim!',
                confirmButtonColor: '#059669'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAbsensi(decodedText, 'qr');
                }
            });
        });
    }

    function closeScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                $('#scanner-modal').addClass('hidden').removeClass('flex');
            }).catch(() => {
                $('#scanner-modal').addClass('hidden').removeClass('flex');
            });
        } else {
            $('#scanner-modal').addClass('hidden').removeClass('flex');
        }
    }

    function submitAbsensi(code, metode) {
        if (!code) return;
        
        const btn = $('#btn-absen');
        btn.prop('disabled', true).text('Memproses...');

        // 1. Pelacak Sesi berdasarkan kode
        $.ajax({
            url: '/api/sesi-kuliah?is_active=1&search=' + code,
            type: 'GET',
            success: function(res) {
                const session = res.data.data.find(s => s.kode_unik === code.toUpperCase() || s.qr_code === code.toUpperCase());
                
                if(!session) {
                    Swal.fire('Gagal!', 'Kode unik tidak valid atau sesi sudah berakhir.', 'error');
                    btn.prop('disabled', false).text('Absen Sekarang');
                    return;
                }

                // 2. Kirim Presensi
                const payload = {
                    sesi_id: session.id,
                    mahasiswa_id: user.mahasiswa.id,
                    metode: metode,
                    status: 'hadir'
                };

                $.ajax({
                    url: '/api/presensi',
                    type: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: function() {
                        $('#kode_unik').val('');
                        historyTable.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Kehadiran Anda telah dicatat oleh sistem.',
                            confirmButtonColor: '#059669'
                        });
                    },
                    error: function(err) {
                        const msg = err.responseJSON?.message || 'Gagal menyimpan absensi.';
                        Swal.fire('Gagal', msg, 'error');
                    },
                    complete: () => btn.prop('disabled', false).text('Absen Sekarang')
                });
            },
            error: () => {
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                btn.prop('disabled', false).text('Absen Sekarang');
            }
        });
    }

    function loadActiveSessions() {
        if (!user.mahasiswa?.id) return;

        // Fetch active sessions + check if already attended
        const url = `/api/sesi-kuliah?is_active=1&include=mataKuliah.dosen.user&mahasiswa_id=${user.mahasiswa.id}`;
        
        $.get(url, function(res) {
            const sessions = res.data.data;
            const container = $('#active-sessions-section');
            const list = $('#active-sessions-list');
            
            if (sessions.length > 0) {
                // Check attendance for these sessions
                $.get(`/api/presensi?mahasiswa_id=${user.mahasiswa.id}`, function(presRes) {
                    const attendedSesiIds = presRes.data.data.map(p => p.sesi_id);
                    
                    const pendingSessions = sessions.filter(s => {
                        const notAttended = !attendedSesiIds.includes(s.id);
                        return notAttended;
                    });
                    
                    if (pendingSessions.length > 0) {
                        container.removeClass('hidden');
                        list.empty();
                        
                        pendingSessions.forEach(sesi => {
                            const mk = sesi.mata_kuliah;
                            const dosen = mk?.dosen?.user?.nama || 'Dosen';
                            
                            list.append(`
                                <div class="bg-white border-2 border-indigo-100 rounded-2xl p-4 shadow-md hover:shadow-lg transition-all flex justify-between items-center group relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-1 bg-indigo-50 text-[8px] font-bold text-indigo-400 uppercase tracking-widest rounded-bl-lg">AKTIF</div>
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 leading-tight">${mk.nama}</h4>
                                            <p class="text-[10px] text-slate-500 mt-0.5">${dosen}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                <span class="text-[10px] font-mono text-indigo-500 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100">${sesi.jam_mulai} - ${sesi.jam_selesai}</span>
                                                ${sesi.gedung ? `<span class="text-[10px] font-medium text-indigo-700 bg-indigo-50/50 px-1.5 py-0.5 rounded border border-indigo-100/50">📍 ${sesi.gedung}, ${sesi.lantai}, ${sesi.ruangan}</span>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <button onclick="copyToAbsen('${sesi.kode_unik}')" class="bg-slate-50 hover:bg-emerald-50 text-slate-800 hover:text-emerald-700 px-3 py-2 rounded-xl border border-slate-200 hover:border-emerald-200 transition-all font-mono font-black tracking-widest text-lg shadow-inner group/btn">
                                            ${sesi.kode_unik}
                                            <div class="text-[7px] text-slate-300 group-hover/btn:text-emerald-400 font-sans tracking-normal font-bold uppercase mt-0.5">Klik untuk Absen</div>
                                        </button>
                                        <button onclick="requestLeave(${sesi.id})" class="text-[10px] text-amber-600 hover:text-white hover:bg-amber-600 px-3 py-1.5 rounded-lg border border-amber-200 hover:border-amber-600 transition-all font-bold uppercase tracking-wider active:scale-95 shadow-sm bg-amber-50/50">
                                            Ajukan Izin/Sakit
                                        </button>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        container.addClass('hidden');
                    }
                });
            } else {
                container.addClass('hidden');
            }
        });
    }

    function copyToAbsen(code) {
        $('#kode_unik').val(code);
        // Scroll to form
        $('#absen-form')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Pulse effect on form
        const formCard = $('#absen-form').parent();
        formCard.addClass('ring-4 ring-emerald-500 ring-opacity-50');
        setTimeout(() => formCard.removeClass('ring-4 ring-emerald-500 ring-opacity-50'), 1500);
        
        showNotification('Kode Disalin', 'Kode absensi telah dipindahkan ke form.', 'info');
    }

    function loadEnrolledCourses() {
        if (!user.mahasiswa?.id) return;

        $.get(`/api/mata-kuliah?include=dosen.user`, function(res) {
            const courses = res.data.data;
            const container = $('#enrolled-courses-list');
            container.empty();

            if (courses.length > 0) {
                courses.forEach(mk => {
                    const dosenName = mk.dosen?.user?.nama || 'Belum ditentukan';
                    container.append(`
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-slate-100/70 hover:border-indigo-100 transition-all flex flex-col gap-2 group shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-grow">
                                    <h4 class="font-bold text-slate-800 text-sm leading-snug group-hover:text-indigo-600 transition-colors">${mk.nama}</h4>
                                    <span class="text-[10px] text-slate-400 font-mono tracking-wider">${mk.kode}</span>
                                </div>
                                <span class="bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded text-[10px] whitespace-nowrap border border-indigo-100">${mk.sks} SKS</span>
                            </div>
                            <div class="h-px bg-slate-200/50 my-1"></div>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="flex items-center gap-1 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    ${dosenName}
                                </span>
                                <span class="font-bold text-indigo-500 bg-indigo-50/50 px-1.5 py-0.5 rounded text-[10px] border border-indigo-100/30">Sem ${mk.semester}</span>
                            </div>
                        </div>
                    `);
                });
            } else {
                container.html(`
                    <div class="text-center py-6">
                        <p class="text-xs text-slate-400 italic">Anda belum terdaftar di mata kuliah manapun.</p>
                    </div>
                `);
            }
        });
    }

    function requestLeave(sesiId) {
        Swal.fire({
            title: 'Pengajuan Izin / Sakit',
            html: `
                <div class="space-y-4 text-left">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status Kehadiran</label>
                        <select id="leave-status" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-emerald-500 bg-white font-semibold">
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan / Alasan</label>
                        <textarea id="leave-reason" placeholder="Tulis alasan izin atau keterangan sakit Anda..." class="w-full rounded-lg border border-slate-300 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500 h-24 transition-all"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Ajukan Izin / Sakit',
            confirmButtonColor: '#d97706',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const status = $('#leave-status').val();
                const keterangan = $('#leave-reason').val();
                if (!keterangan) {
                    Swal.showValidationMessage('Keterangan / alasan wajib diisi!');
                }
                return { status, keterangan };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { status, keterangan } = result.value;
                const pendingStatus = status === 'izin' ? 'pending_izin' : 'pending_sakit';
                const payload = {
                    sesi_id: sesiId,
                    mahasiswa_id: user.mahasiswa.id,
                    metode: 'manual',
                    status: pendingStatus,
                    keterangan: keterangan
                };

                $.ajax({
                    url: '/api/presensi',
                    type: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: function() {
                        historyTable.ajax.reload();
                        loadActiveSessions();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pengajuan izin/sakit Anda telah berhasil dikirim.',
                            confirmButtonColor: '#059669'
                        });
                    },
                    error: function(err) {
                        const msg = err.responseJSON?.message || 'Gagal mengajukan izin.';
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
