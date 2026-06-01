@extends('layouts.app')
@section('title', 'Data Dosen')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Dosen</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola data induk tenaga pengajar (dosen).</p>
        </div>
        <button id="btn-add-dosen" onclick="openModal('add')" class="hidden w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Data
        </button>
    </div>

    <div class="p-0 sm:p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="dosen-table" class="w-full text-left text-sm border-collapse">
                <thead class="bg-indigo-50/50 text-indigo-700 font-bold border-b border-indigo-100">
                    <tr>
                        <th class="p-4 uppercase tracking-wider text-[11px]">ID</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">NIP</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Nama Lengkap (User)</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Jurusan</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Jabatan</th>
                        <th class="p-4 text-right uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="dosen-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 transform scale-95 opacity-0 transition-all duration-300" id="modal-content">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Tambah Dosen</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="dosen-form" class="p-5">
            <input type="hidden" id="form-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <p class="text-[10px] text-slate-400 mt-1" id="hint-password">Kosongkan jika tidak ingin mengubah password</p>
                    </div>
                </div>
                <hr class="border-slate-100">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">NIP</label>
                    <input type="text" id="nip" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jurusan</label>
                    <select id="jurusan_id" name="jurusan_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">-- Pilih Jurusan --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
                    <input type="text" id="jabatan" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">Batal</button>
                <button type="submit" id="btn-submit" class="px-4 py-2 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table, submitMode = 'add'; 

    $(document).ready(function() {
        if (JSON.parse(localStorage.getItem('user'))?.role === 'admin') {
            $('#btn-add-dosen').removeClass('hidden');
        }
        table = $('#dosen-table').DataTable({
            ajax: { url: '/api/dosen?include=user,jurusan', dataSrc: 'data.data' },
            columns: [
                { data: 'id', className: 'p-4 text-slate-500 font-mono' },
                { data: 'nip', className: 'p-4 font-semibold text-slate-700' },
                { 
                    data: 'user.nama', 
                    className: 'p-4',
                    render: data => data ? `<div class="flex flex-col"><span class="font-bold text-slate-800">${data}</span><span class="text-[10px] text-slate-400">Dosen Aktif</span></div>` : '<span class="text-slate-300 italic">N/A</span>' 
                },
                { 
                    data: 'jurusan.nama', 
                    className: 'p-4 text-slate-600',
                    defaultContent: '<span class="text-slate-300 italic">N/A</span>'
                },
                { data: 'jabatan', className: 'p-4 text-slate-600' },
                {
                    data: null, className: 'p-4 text-right', orderable: false,
                    visible: JSON.parse(localStorage.getItem('user'))?.role === 'admin',
                    render: function(data, type, row) {
                        return `
                            <div class="flex justify-end gap-2">
                                <button onclick="openModal('edit', ${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-sky-600 bg-sky-50 hover:bg-sky-600 hover:text-white px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span class="hidden xs:inline">EDIT</span>
                                </button>
                                <button onclick="deleteData(${row.id})" class="inline-flex items-center gap-1 sm:gap-1.5 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-2 sm:px-3 py-1.5 rounded-lg transition-all text-[10px] sm:text-xs font-bold shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span class="hidden xs:inline">DELETE</span>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Cari dosen...",
                lengthMenu: "_MENU_",
            },
            drawCallback: function() {
                $('.dataTables_paginate').addClass('flex justify-end gap-1 mt-4 p-4');
                $('.dataTables_info').addClass('text-xs text-slate-400 p-4');
            }
        });

        $('#dosen-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#form-id').val();
            
            $.ajax({
                url: submitMode === 'add' ? '/api/dosen' : `/api/dosen/${id}`,
                type: submitMode === 'add' ? 'POST' : 'PUT',
                data: JSON.stringify({
                    nama: $('#nama').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    nip: $('#nip').val(),
                    jabatan: $('#jabatan').val(),
                    jurusan_id: $('#jurusan_id').val()
                }),
                contentType: 'application/json',
                success: (res) => {
                    closeModal();
                    table.ajax.reload();
                    showNotification('Berhasil', res.message);
                },
                error: (err) => Swal.fire('Gagal!', err.responseJSON?.message || 'Error', 'error')
            });
        });
    });

    function openModal(mode, id = null) {
        submitMode = mode;
        $('#dosen-form')[0].reset();
        $('#form-id').val('');
        
        loadJurusanOptions().then(() => {
            $('#modal-title').text(mode === 'add' ? 'Tambah Dosen' : 'Edit Dosen');
            
            if(mode === 'add') {
                $('#password').prop('required', true);
                $('#hint-password').addClass('hidden');
            } else {
                $('#password').prop('required', false);
                $('#hint-password').removeClass('hidden');
                loadDataForEdit(id);
            }
        });
        $('#dosen-modal').removeClass('hidden').addClass('flex');
        setTimeout(() => $('#modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    function closeModal() {
        $('#modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#dosen-modal').addClass('hidden').removeClass('flex'), 300);
    }

    function loadDataForEdit(id) {
        $.get(`/api/dosen/${id}?include=user`, res => {
            $('#form-id').val(res.data.id);
            $('#nama').val(res.data.user.nama);
            $('#email').val(res.data.user.email);
            $('#nip').val(res.data.nip);
            $('#jabatan').val(res.data.jabatan);
            $('#jurusan_id').val(res.data.jurusan_id);
        });
    }

    function deleteData(id) {
        Swal.fire({ title: 'Hapus Data?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444' })
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/dosen/${id}`, type: 'DELETE',
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
</script>
@endpush
