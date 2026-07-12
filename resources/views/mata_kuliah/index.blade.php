@extends('layouts.app')
@section('title', 'Data Mata Kuliah')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Master Mata Kuliah</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola data mata kuliah beserta dosen pengajarnya.</p>
        </div>
        <button id="btn-add-matkul" onclick="openModal('add')" class="hidden w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Mata Kuliah
        </button>
    </div>

    <div class="p-0 sm:p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="matkul-table" class="w-full text-left text-sm border-collapse">
                <thead class="bg-indigo-50/50 text-indigo-700 font-bold border-b border-indigo-100">
                    <tr>
                        <th class="p-4 uppercase tracking-wider text-[11px]">ID</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Kode</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Nama Mata Kuliah</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Jurusan</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">SKS</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Semester</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Dosen (NIP)</th>
                        <th class="p-4 text-right uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="matkul-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 transform scale-95 opacity-0 transition-all duration-300" id="modal-content">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Tambah Mata Kuliah</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="matkul-form" class="p-5">
            <input type="hidden" id="form-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode MK</label>
                    <input type="text" id="kode" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama MK</label>
                    <input type="text" id="nama" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="flex gap-4">
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">SKS</label>
                        <input type="number" id="sks" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Semester</label>
                        <input type="number" id="semester" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jurusan</label>
                    <select id="jurusan_id" name="jurusan_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">-- Pilih Jurusan --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dosen Pengampu</label>
                    <select id="dosen_id" name="dosen_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">-- Pilih Dosen --</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">Batal</button>
                <button type="submit" id="btn-submit" class="px-4 py-2 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Kelas Paralel Modal -->
<div id="kelas-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 transform scale-95 opacity-0 transition-all duration-300" id="kelas-modal-content">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" id="kelas-modal-title">Kelola Kelas Paralel</h3>
            <button onclick="closeKelasModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-5">
            <form id="kelas-form" class="flex gap-2 mb-4">
                <input type="hidden" id="kelas-form-matkul-id">
                <input type="hidden" id="kelas-form-id">
                <input type="text" id="kelas-nama" required placeholder="Nama Kelas (A, B, Reguler...)" class="flex-grow rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold shadow-md transition-all active:scale-95">Tambah</button>
            </form>
            <div id="kelas-list" class="space-y-2">
                <p class="text-slate-400 text-sm italic">Memuat...</p>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Modal -->
<div id="enrollment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-slate-800" id="enrollment-modal-title">Daftar Mahasiswa</h3>
                <p class="text-xs text-slate-500 mt-1" id="enrollment-modal-subtitle"></p>
            </div>
            <button onclick="closeEnrollmentModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="enrollment-add-section" class="p-6 bg-slate-50 border-b border-slate-100">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Tambah Mahasiswa ke Kelas</label>
            <div class="flex gap-2 mb-3">
                <div class="w-1/3">
                    <select id="enroll-kelas-paralel-id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 appearance-none bg-white">
                        <option value="">-- Semua Kelas --</option>
                    </select>
                </div>
                <div class="relative flex-grow">
                    <select id="select-mahasiswa" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 appearance-none bg-white">
                        <option value="">-- Cari/Pilih Mahasiswa --</option>
                    </select>
                </div>
                <button onclick="enrollStudent()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold shadow-md transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    TAMBAH
                </button>
            </div>
        </div>

        <div class="flex-grow overflow-y-auto p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-white text-slate-400 font-bold uppercase text-[10px] sticky top-0 z-10 border-b border-slate-100">
                    <tr>
                        <th class="p-4">Mahasiswa</th>
                        <th class="p-4">Kelas</th>
                        <th class="p-4">Tahun Ajaran</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="enrollment-list" class="divide-y divide-slate-50 bg-white"></tbody>
            </table>
            <div id="enrollment-empty" class="hidden p-12 text-center bg-white">
                <p class="text-slate-400 font-medium italic">Belum ada mahasiswa yang terdaftar di mata kuliah ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table, submitMode = 'add', dosenOptionsLoaded = false;

    $(document).ready(function() {
        const user = JSON.parse(localStorage.getItem('user'));
        
        if (user?.role === 'admin') {
            $('#btn-add-matkul').removeClass('hidden');
        } else if (user?.role === 'mahasiswa') {
            $('h1').text('Mata Kuliah Anda');
            $('h1').next().text('Daftar mata kuliah yang sedang Anda pelajari semester ini.');
        }

        let ajaxUrl = '/api/mata-kuliah?include=dosen.user,jurusan';
        
        console.log('Current User:', user);

        if (user?.role === 'dosen') {
            const dosenId = user.dosen?.id;
            console.log('Dosen ID detected:', dosenId);
            if (dosenId) {
                ajaxUrl += '&dosen_id=' + dosenId;
            } else {
                console.warn('Dosen ID not found in user object. Filtering may not work. Please re-login.');
                ajaxUrl += '&dosen_id=0';
            }
        }

        // Enrollment controls only for admin
        if (user?.role !== 'admin') {
            $('#enrollment-add-section').hide();
        }

        // Filter dosen by jurusan when jurusan changes
        $('#jurusan_id').on('change', function() {
            const jurusanId = $(this).val();
            dosenOptionsLoaded = false;
            loadDosenOptions(jurusanId || null);
        });

        table = $('#matkul-table').DataTable({
            ajax: { url: ajaxUrl + '&per_page=500', dataSrc: 'data.data' },
            columns: [
                { data: 'id', className: 'p-4 text-slate-500 font-mono' },
                { data: 'kode', className: 'p-4 font-bold text-indigo-600' },
                { data: 'nama', className: 'p-4 font-semibold text-slate-800' },
                { 
                    data: 'jurusan.nama', 
                    className: 'p-4 text-slate-600',
                    defaultContent: '<span class="text-slate-300 italic">N/A</span>'
                },
                { 
                    data: 'sks', 
                    className: 'p-4',
                    render: data => `<span class="bg-emerald-50 text-emerald-700 px-2 py-1 rounded text-xs font-bold border border-emerald-100">${data} SKS</span>`
                },
                { 
                    data: 'semester', 
                    className: 'p-4',
                    render: data => `<span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold border border-blue-100">Sem ${data}</span>`
                },
                { 
                    data: 'dosen.nip', 
                    className: 'p-4',
                    defaultContent: '<span class="text-slate-300 italic">Belum diset</span>',
                    render: (data, type, row) => data ? `<div class="flex flex-col"><span class="text-slate-700 font-medium">${row.dosen.user?.nama || 'Dosen'}</span><span class="text-[10px] text-slate-400 font-mono">${data}</span></div>` : null
                },
                {
                    data: null, className: 'p-4 text-right', orderable: false,
                    visible: ['admin', 'dosen'].includes(JSON.parse(localStorage.getItem('user'))?.role),
                    render: function(data, type, row) {
                        const userRole = JSON.parse(localStorage.getItem('user'))?.role;
                        
                        let buttons = `
                            <button onclick="openEnrollmentModal(${row.id}, '${row.nama}', ${row.jurusan_id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-emerald-600 bg-emerald-50 hover:bg-emerald-600 hover:text-white px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <span class="hidden xs:inline">MAHASISWA</span>
                            </button>
                        `;

                        if (userRole === 'admin') {
                            buttons += `
                                <button onclick="openKelasModal('add', ${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-amber-600 bg-amber-50 hover:bg-amber-600 hover:text-white px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    <span class="hidden xs:inline">KELAS</span>
                                </button>
                                <button onclick="openModal('edit', ${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-sky-600 bg-sky-50 hover:bg-sky-600 hover:text-white px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span class="hidden xs:inline">EDIT</span>
                                </button>
                                <button onclick="deleteData(${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span class="hidden xs:inline">HAPUS</span>
                                </button>
                            `;
                        }

                        return `
                             <div class="flex items-center justify-end gap-2">
                                ${buttons}
                            </div>
                        `;
                    }
                }
            ],
            language: { 
                search: "",
                searchPlaceholder: "Cari mata kuliah...",
                lengthMenu: "_MENU_",
            },
            drawCallback: function() {
                $('.dataTables_paginate').addClass('flex justify-end gap-1 mt-4 p-4');
                $('.dataTables_info').addClass('text-xs text-slate-400 p-4');
            }
        });

        $('#matkul-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#form-id').val();
            
            $.ajax({
                url: submitMode === 'add' ? '/api/mata-kuliah' : `/api/mata-kuliah/${id}`,
                type: submitMode === 'add' ? 'POST' : 'PUT',
                data: JSON.stringify({
                    kode: $('#kode').val(),
                    nama: $('#nama').val(),
                    sks: $('#sks').val(),
                    semester: $('#semester').val(),
                    dosen_id: $('#dosen_id').val(),
                    jurusan_id: $('#jurusan_id').val()
                }),
                contentType: 'application/json',
                success: (res) => {
                    closeModal();
                    if (submitMode === 'add') {
                        table.order([0, 'desc']).draw(); // Sort by newest first (descending ID)
                    } else {
                        table.ajax.reload();
                    }
                    showNotification('Berhasil', res.message);
                },
                error: (err) => {
                    const message = err.responseJSON?.message || 'Error';
                    const errors = err.responseJSON?.errors;
                    let detail = message;
                    if (errors) {
                        const errorList = Object.values(errors).flat();
                        detail = errorList.join('\n');
                    }
                    Swal.fire('Gagal!', detail, 'error');
                }
            });
        });

    });

    function openModal(mode, id = null) {
        submitMode = mode;
        $('#matkul-form')[0].reset();
        $('#form-id').val('');
        $('#modal-title').text(mode === 'add' ? 'Tambah Mata Kuliah' : 'Edit Mata Kuliah');
        
        Promise.all([loadDosenOptions(), loadJurusanOptions()]).then(() => {
            if(mode === 'edit') {
                $.get(`/api/mata-kuliah/${id}`, res => {
                    $('#form-id').val(res.data.id);
                    $('#kode').val(res.data.kode);
                    $('#nama').val(res.data.nama);
                    $('#sks').val(res.data.sks);
                    $('#semester').val(res.data.semester);
                    $('#jurusan_id').val(res.data.jurusan_id);
                    dosenOptionsLoaded = false;
                    loadDosenOptions(res.data.jurusan_id).then(() => {
                        $('#dosen_id').val(res.data.dosen_id);
                    });
                });
            }
        });
        $('#matkul-modal').removeClass('hidden').addClass('flex');
        setTimeout(() => $('#modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    function closeModal() {
        $('#modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#matkul-modal').addClass('hidden').removeClass('flex'), 300);
    }

    function loadDosenOptions(jurusanId = null) {
        return new Promise((resolve) => {
            if (dosenOptionsLoaded && !jurusanId) return resolve();
            let url = '/api/dosen?include=user&per_page=100';
            if (jurusanId) url += `&jurusan_id=${jurusanId}`;
            $.get(url, function(res) {
                const select = $('#dosen_id');
                select.empty().append('<option value="">-- Pilih Dosen --</option>');
                res.data.data.forEach(dosen => {
                    const name = dosen.user ? dosen.user.nama : 'Tanpa Nama';
                    select.append(`<option value="${dosen.id}">${name} (${dosen.nip})</option>`);
                });
                dosenOptionsLoaded = true;
                resolve();
            }).fail(() => {
                showNotification('Error', 'Gagal memuat daftar dosen', 'error');
                resolve();
            });
        });
    }

    function deleteData(id) {
        Swal.fire({ title: 'Hapus Data?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444' })
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/mata-kuliah/${id}`, type: 'DELETE',
                    success: (res) => { table.ajax.reload(); showNotification('Terhapus', res.message); }
                });
            }
        });
    }

    let jurusanOptionsLoaded = false;
    function loadJurusanOptions() {
        return new Promise((resolve) => {
            if (jurusanOptionsLoaded) return resolve();
            $.get('/api/jurusan?per_page=100', function(res) {
                const select = $('#jurusan_id');
                select.empty().append('<option value="">-- Pilih Jurusan --</option>');
                res.data.data.forEach(item => {
                    select.append(`<option value="${item.id}">${item.nama} (${item.kode})</option>`);
                });
                jurusanOptionsLoaded = true;
                resolve();
            }).fail(() => {
                showNotification('Error', 'Gagal memuat daftar jurusan', 'error');
                resolve();
            });
        });
    }

    // --- Enrollment Logic ---
    let currentMatkulId = null;
    let currentJurusanId = null;

    function openEnrollmentModal(id, name, jurusanId) {
        currentMatkulId = id;
        currentJurusanId = jurusanId;
        $('#enrollment-modal-subtitle').text(name);
        $('#enrollment-list').empty();
        $('#enrollment-empty').addClass('hidden');
        $('#enrollment-modal').removeClass('hidden').addClass('flex');
        
        loadKelasEnrollOptions(id);
        loadEnrollments();
        loadMahasiswaByJurusan(jurusanId);
    }

    function loadKelasEnrollOptions(mkId) {
        const kelasSelect = $('#enroll-kelas-paralel-id');
        $.get(`/api/kelas-paralel?mata_kuliah_id=${mkId}`, function(res) {
            const data = res.data?.data || [];
            kelasSelect.empty().append('<option value="">-- Semua Kelas --</option>');
            data.forEach(k => {
                kelasSelect.append(`<option value="${k.id}">${k.nama_kelas}</option>`);
            });
        }).fail(() => {
            kelasSelect.empty().append('<option value="">-- Gagal memuat --</option>');
        });
    }

    function closeEnrollmentModal() {
        $('#enrollment-modal').addClass('hidden').removeClass('flex');
    }

    function loadEnrollments() {
        const userRole = JSON.parse(localStorage.getItem('user'))?.role;
        const kelasId = $('#enroll-kelas-paralel-id').val();
        let url = `/api/enrollment?mata_kuliah_id=${currentMatkulId}&include=mahasiswa.user,kelasParalel`;
        if (kelasId) url += `&kelas_paralel_id=${kelasId}`;
        $.get(url, function(res) {
            const list = $('#enrollment-list');
            list.empty();
            if (res.data.data.length === 0) {
                $('#enrollment-empty').removeClass('hidden');
            } else {
                $('#enrollment-empty').addClass('hidden');
                res.data.data.forEach(e => {
                    const studentName = e.mahasiswa?.user?.nama || 'N/A';
                    const kelasLabel = e.kelas_paralel?.nama_kelas || '<span class="text-slate-300 italic">-</span>';
                    const deleteBtn = userRole === 'admin'
                        ? `<button onclick="unenrollStudent(${e.id})" class="btn-unenroll text-rose-500 hover:text-rose-700 p-2 rounded-lg hover:bg-rose-50 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>`
                        : '';
                    list.append(`
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[10px] uppercase">
                                        ${studentName.charAt(0)}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700">${studentName}</span>
                                        <span class="text-[10px] text-slate-400 font-mono tracking-widest">${e.mahasiswa?.nim || ''}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 font-medium text-slate-500">${kelasLabel}</td>
                            <td class="p-4 font-medium text-slate-500">${e.tahun_ajaran}</td>
                            <td class="p-4 text-right">${deleteBtn}</td>
                        </tr>
                    `);
                });
            }
        });
    }

    // Re-load enrollments when kelas filter changes
    $('#enroll-kelas-paralel-id').on('change', function() {
        if (currentMatkulId) loadEnrollments();
    });

    function loadMahasiswaByJurusan(jurusanId) {
        const url = jurusanId 
            ? `/api/mahasiswa?jurusan_id=${jurusanId}&per_page=500&include=user`
            : '/api/mahasiswa?per_page=500&include=user';
        
        $.get(url, function(res) {
            const select = $('#select-mahasiswa');
            select.empty().append('<option value="">-- Cari/Pilih Mahasiswa --</option>');
            res.data.data.forEach(m => {
                select.append(`<option value="${m.id}">${m.user?.nama} (${m.nim})</option>`);
            });
        }).fail(() => {
            showNotification('Error', 'Gagal memuat daftar mahasiswa', 'error');
        });
    }

    function getTahunAjaran() {
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth() + 1;
        return month >= 7 ? `${year}/${year + 1}` : `${year - 1}/${year}`;
    }

    function enrollStudent() {
        const mahasiswaId = $('#select-mahasiswa').val();
        if (!mahasiswaId) return showNotification('Peringatan', 'Pilih mahasiswa terlebih dahulu', 'warning');

        const kelasParalelId = $('#enroll-kelas-paralel-id').val();

        $.ajax({
            url: '/api/enrollment',
            type: 'POST',
            data: JSON.stringify({
                mahasiswa_id: mahasiswaId,
                mata_kuliah_id: currentMatkulId,
                kelas_paralel_id: kelasParalelId || null,
                tahun_ajaran: getTahunAjaran()
            }),
            contentType: 'application/json',
            success: function() {
                showNotification('Berhasil', 'Mahasiswa berhasil ditambahkan ke kelas');
                loadEnrollments();
                $('#select-mahasiswa').val('');
            },
            error: (err) => {
                const message = err.responseJSON?.message || 'Error tidak diketahui';
                const errors = err.responseJSON?.errors;
                if (errors && errors.mata_kuliah_id) {
                    Swal.fire('Validasi Gagal', errors.mata_kuliah_id[0], 'warning');
                } else {
                    Swal.fire('Gagal', message, 'error');
                }
            }
        });
    }

    // --- Kelas Paralel CRUD ---
    let currentKelasMatkulId = null;

    function openKelasModal(mode, matkulId) {
        currentKelasMatkulId = matkulId;
        $('#kelas-form-matkul-id').val(matkulId);
        $('#kelas-form-id').val('');
        $('#kelas-nama').val('');
        $('#kelas-modal-title').text('Kelola Kelas Paralel');
        loadKelasList(matkulId);
        $('#kelas-modal').removeClass('hidden').addClass('flex');
        setTimeout(() => $('#kelas-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    function closeKelasModal() {
        $('#kelas-modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#kelas-modal').addClass('hidden').removeClass('flex'), 300);
    }

    function loadKelasList(matkulId) {
        $('#kelas-list').html('<p class="text-slate-400 text-sm italic">Memuat...</p>');
        $.get(`/api/kelas-paralel?mata_kuliah_id=${matkulId}`, function(res) {
            const data = res.data?.data || [];
            if (data.length === 0) {
                $('#kelas-list').html('<p class="text-slate-400 text-sm italic">Belum ada kelas. Tambah kelas baru di atas.</p>');
                return;
            }
            let html = '';
            data.forEach(k => {
                html += `
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold rounded-lg">${k.nama_kelas}</span>
                            <div>
                                <span class="font-semibold text-slate-700">Kelas ${k.nama_kelas}</span>
                                ${k.dosen ? `<span class="text-xs text-slate-400 ml-2">Dosen: ${k.dosen.user?.nama || ''}</span>` : ''}
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button onclick="editKelas(${k.id}, '${k.nama_kelas}')" class="text-sky-600 hover:text-sky-800 p-1.5 rounded-lg hover:bg-sky-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button onclick="deleteKelas(${k.id})" class="text-red-600 hover:text-red-800 p-1.5 rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                `;
            });
            $('#kelas-list').html(html);
        }).fail(() => {
            $('#kelas-list').html('<p class="text-red-400 text-sm">Gagal memuat data kelas.</p>');
        });
    }

    $('#kelas-form').on('submit', function(e) {
        e.preventDefault();
        const matkulId = $('#kelas-form-matkul-id').val();
        const kelasId = $('#kelas-form-id').val();
        const nama = $('#kelas-nama').val();
        if (!nama) return;

        const url = kelasId ? `/api/kelas-paralel/${kelasId}` : '/api/kelas-paralel';
        const method = kelasId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify({ mata_kuliah_id: parseInt(matkulId), nama_kelas: nama }),
            contentType: 'application/json',
            success: () => {
                $('#kelas-nama').val('');
                $('#kelas-form-id').val('');
                loadKelasList(matkulId);
                showNotification('Berhasil', kelasId ? 'Kelas diperbarui' : 'Kelas ditambahkan');
            },
            error: (err) => {
                const msg = err.responseJSON?.errors?.nama_kelas?.[0] || err.responseJSON?.message || 'Error';
                Swal.fire('Gagal!', msg, 'error');
            }
        });
    });

    function editKelas(id, nama) {
        $('#kelas-form-id').val(id);
        $('#kelas-nama').val(nama);
        $('#kelas-nama').focus();
    }

    function deleteKelas(id) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: 'Jadwal dan enrollment terkait akan kehilangan referensi kelas ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/kelas-paralel/${id}`,
                    type: 'DELETE',
                    success: () => {
                        loadKelasList(currentKelasMatkulId);
                        showNotification('Terhapus', 'Kelas berhasil dihapus');
                    }
                });
            }
        });
    }

    function unenrollStudent(id) {
        Swal.fire({
            title: 'Hapus Pendaftaran?',
            text: "Mahasiswa tidak akan lagi terdaftar di mata kuliah ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/enrollment/${id}`,
                    type: 'DELETE',
                    success: function() {
                        showNotification('Terhapus', 'Mahasiswa berhasil dihapus dari kelas');
                        loadEnrollments();
                    }
                });
            }
        });
    }
</script>
@endpush
