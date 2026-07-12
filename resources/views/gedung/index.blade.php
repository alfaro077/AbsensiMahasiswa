@extends('layouts.app')
@section('title', 'Data Gedung')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Master Gedung</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola data gedung untuk penempatan ruangan kuliah.</p>
        </div>
        <button id="btn-add" onclick="openModal('add')" class="hidden w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Gedung
        </button>
    </div>

    <div class="p-0 sm:p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="gedung-table" class="w-full text-left text-sm border-collapse">
                <thead class="bg-indigo-50/50 text-indigo-700 font-bold border-b border-indigo-100">
                    <tr>
                        <th class="p-4 uppercase tracking-wider text-[11px]">ID</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Kode</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Nama Gedung</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Lokasi</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Jumlah Jadwal</th>
                        <th class="p-4 text-right uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="gedung-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 transform scale-95 opacity-0 transition-all duration-300" id="modal-content">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" id="modal-title">Tambah Gedung</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="gedung-form" class="p-5">
            <input type="hidden" id="form-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode Gedung</label>
                    <input type="text" id="kode" placeholder="Contoh: GDA" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Gedung</label>
                    <input type="text" id="nama" placeholder="Contoh: Gedung A" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi</label>
                    <input type="text" id="lokasi" placeholder="Contoh: Jl. Kampus No. 1" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
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
        const user = JSON.parse(localStorage.getItem('user'));

        if (user?.role === 'admin') {
            $('#btn-add').removeClass('hidden');
        }

        table = $('#gedung-table').DataTable({
            ajax: { url: '/api/gedung?per_page=500', dataSrc: 'data.data' },
            columns: [
                { data: 'id', className: 'p-4 text-slate-500 font-mono' },
                { data: 'kode', className: 'p-4 font-bold text-indigo-600' },
                { data: 'nama', className: 'p-4 font-semibold text-slate-800' },
                { 
                    data: 'lokasi', 
                    className: 'p-4 text-slate-600',
                    defaultContent: '<span class="text-slate-300 italic">-</span>'
                },
                { 
                    data: 'jadwal_count', 
                    className: 'p-4',
                    render: data => `<span class="bg-sky-50 text-sky-700 px-2 py-1 rounded text-xs font-bold border border-sky-100">${data ?? 0} Jadwal</span>`
                },
                {
                    data: null, className: 'p-4 text-right', orderable: false,
                    visible: user?.role === 'admin',
                    render: function(data) {
                        return `
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openModal('edit', ${data.id})" class="inline-flex items-center gap-1 text-sky-600 bg-sky-50 hover:bg-sky-600 hover:text-white px-2 py-1.5 rounded-lg transition-all text-xs font-bold shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    EDIT
                                </button>
                                <button onclick="deleteData(${data.id})" class="inline-flex items-center gap-1 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-2 py-1.5 rounded-lg transition-all text-xs font-bold shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    HAPUS
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: { 
                search: "",
                searchPlaceholder: "Cari gedung...",
                lengthMenu: "_MENU_",
            },
            drawCallback: function() {
                $('.dataTables_paginate').addClass('flex justify-end gap-1 mt-4 p-4');
                $('.dataTables_info').addClass('text-xs text-slate-400 p-4');
            }
        });

        $('#gedung-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#form-id').val();

            $.ajax({
                url: submitMode === 'add' ? '/api/gedung' : `/api/gedung/${id}`,
                type: submitMode === 'add' ? 'POST' : 'PUT',
                data: JSON.stringify({
                    kode: $('#kode').val(),
                    nama: $('#nama').val(),
                    lokasi: $('#lokasi').val()
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
        $('#gedung-form')[0].reset();
        $('#form-id').val('');
        $('#modal-title').text(mode === 'add' ? 'Tambah Gedung' : 'Edit Gedung');

        if(mode === 'edit') {
            $.get(`/api/gedung/${id}`, res => {
                $('#form-id').val(res.data.id);
                $('#kode').val(res.data.kode);
                $('#nama').val(res.data.nama);
                $('#lokasi').val(res.data.lokasi);
            });
        }

        $('#gedung-modal').removeClass('hidden').addClass('flex');
        setTimeout(() => $('#modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    function closeModal() {
        $('#modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#gedung-modal').addClass('hidden').removeClass('flex'), 300);
    }

    function deleteData(id) {
        Swal.fire({ title: 'Hapus Gedung?', text: 'Gedung akan dihapus secara permanen.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444' })
        .then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/gedung/${id}`, type: 'DELETE',
                    success: (res) => { table.ajax.reload(); showNotification('Terhapus', res.message); }
                });
            }
        });
    }
</script>
@endpush
