@extends('layouts.app')
@section('title', 'Jadwal Kuliah')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Jadwal Kuliah</h1>
            <p id="jadwal-desc" class="text-slate-500 text-sm mt-1">Jadwal perkuliahan untuk setiap mata kuliah termasuk lokasi ruangan.</p>
        </div>
        <button id="btn-add-jadwal" onclick="openJadwalModal('add')" class="hidden w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Tambah Jadwal
        </button>
    </div>

    <div class="p-0 sm:p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="jadwal-table" class="w-full text-left text-sm border-collapse">
                <thead class="bg-indigo-50/50 text-indigo-700 font-bold border-b border-indigo-100">
                    <tr>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Mata Kuliah</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Kelas</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Hari</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Waktu</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Gedung</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Lantai</th>
                        <th class="p-4 uppercase tracking-wider text-[11px]">Ruangan</th>
                        <th class="p-4 text-right uppercase tracking-wider text-[11px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Jadwal Modal -->
<div id="jadwal-modal" class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 transform scale-95 opacity-0 transition-all duration-300" id="jadwal-modal-content">
        <div class="flex justify-between items-center p-5 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800" id="jadwal-modal-title">Tambah Jadwal</h3>
            <button onclick="closeJadwalModal()" class="text-slate-400 hover:text-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="jadwal-form" class="p-5">
            <input type="hidden" id="jadwal-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mata Kuliah</label>
                    <select id="jadwal-mata-kuliah-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Mata Kuliah --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kelas Paralel</label>
                    <select id="jadwal-kelas-paralel-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Mata Kuliah Terlebih Dahulu --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hari</label>
                    <select id="jadwal-hari" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Hari --</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label>
                        <input type="time" id="jadwal-jam-mulai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label>
                        <input type="time" id="jadwal-jam-selesai" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gedung</label>
                    <select id="jadwal-gedung-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Gedung --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Ruangan</label>
                    <select id="jadwal-ruangan-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Pilih Gedung Terlebih Dahulu --</option>
                    </select>
                </div>
                <div id="jadwal-error-container" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-700 font-medium" id="jadwal-error-msg"></p>
                    <div class="text-sm text-red-600 mt-1" id="jadwal-error-list"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeJadwalModal()" class="px-4 py-2 font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200">Batal</button>
                <button type="submit" id="jadwal-btn-submit" class="px-4 py-2 font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let jadwalTable, jadwalSubmitMode = 'add';

    $(document).ready(function() {
        const user = JSON.parse(localStorage.getItem('user'));

        if (user?.role === 'admin') {
            $('#btn-add-jadwal').removeClass('hidden');
            $('#jadwal-desc').text('Kelola jadwal mengajar untuk setiap mata kuliah termasuk lokasi ruangan.');
        } else if (user?.role === 'dosen') {
            $('#jadwal-desc').text('Jadwal mata kuliah yang Anda ampu beserta lokasi ruangan.');
        } else if (user?.role === 'mahasiswa') {
            $('#jadwal-desc').text('Jadwal perkuliahan Anda berdasarkan mata kuliah yang diambil.');
        }

        jadwalTable = $('#jadwal-table').DataTable({
            ajax: { url: '/api/jadwal-mata-kuliah?include=mataKuliah,gedung,ruangan,kelasParalel&per_page=500', dataSrc: 'data.data' },
            columns: [
                { data: 'mata_kuliah.nama', className: 'p-4 font-semibold text-slate-800', defaultContent: 'N/A' },
                { data: null, className: 'p-4 font-medium text-indigo-600', render: (data) => data.kelas_paralel?.nama_kelas || '<span class="text-slate-300 italic">-</span>' },
                { data: 'hari', className: 'p-4 font-medium text-indigo-600' },
                { data: null, className: 'p-4', render: (data) => `${(data.jam_mulai || '').substring(0, 5)} - ${(data.jam_selesai || '').substring(0, 5)}` },
                { data: 'gedung.nama', className: 'p-4 text-slate-700 font-medium', defaultContent: '<span class="text-slate-300 italic">N/A</span>' },
                { data: null, className: 'p-4 text-slate-600', render: (data) => data.ruangan?.lantai || data.lantai || '-' },
                { data: null, className: 'p-4 text-slate-700 font-medium', render: (data) => data.ruangan?.nama || data.ruangan || '-' },
                {
                    data: null, className: 'p-4 text-right', orderable: false,
                    visible: JSON.parse(localStorage.getItem('user'))?.role === 'admin',
                    render: function(data) {
                        return `
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openJadwalModal('edit', ${data.id})" class="inline-flex items-center gap-1 text-sky-600 bg-sky-50 hover:bg-sky-600 hover:text-white px-2 py-1.5 rounded-lg transition-all text-xs font-bold shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    EDIT
                                </button>
                                <button onclick="deleteJadwal(${data.id})" class="inline-flex items-center gap-1 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white px-2 py-1.5 rounded-lg transition-all text-xs font-bold shadow-sm">
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
                searchPlaceholder: "Cari jadwal...",
                lengthMenu: "_MENU_",
            },
            drawCallback: function() {
                $('.dataTables_paginate').addClass('flex justify-end gap-1 mt-4 p-4');
                $('.dataTables_info').addClass('text-xs text-slate-400 p-4');
            }
        });

        // Cascading dropdown: Mata Kuliah -> Kelas Paralel
        $('#jadwal-mata-kuliah-id').on('change', function() {
            const mkId = $(this).val();
            loadKelasParalelOptions(mkId);
        });

        // Cascading dropdown: Gedung -> Ruangan
        $('#jadwal-gedung-id').on('change', function() {
            const gedungId = $(this).val();
            loadRuanganByGedung(gedungId);
        });

        // Jadwal Form Submit
        $('#jadwal-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#jadwal-id').val();
            const data = {
                kelas_paralel_id: parseInt($('#jadwal-kelas-paralel-id').val()),
                hari: $('#jadwal-hari').val(),
                jam_mulai: $('#jadwal-jam-mulai').val(),
                jam_selesai: $('#jadwal-jam-selesai').val(),
                ruangan_id: parseInt($('#jadwal-ruangan-id').val())
            };

            $.ajax({
                url: jadwalSubmitMode === 'add' ? '/api/jadwal-mata-kuliah' : `/api/jadwal-mata-kuliah/${id}`,
                type: jadwalSubmitMode === 'add' ? 'POST' : 'PUT',
                data: JSON.stringify(data),
                contentType: 'application/json',
                success: (res) => {
                    closeJadwalModal();
                    jadwalTable.ajax.reload();
                    showNotification('Berhasil', res.message);
                },
                error: (err) => {
                    if (err.status === 422 && err.responseJSON?.errors) {
                        const errors = err.responseJSON.errors;
                        const errorList = Object.values(errors).flat().map(msg => `• ${msg}`).join('\n');
                        $('#jadwal-error-msg').text(err.responseJSON.message || 'Terjadi bentrok jadwal:');
                        $('#jadwal-error-list').html(errorList.replace(/\n/g, '<br>'));
                        $('#jadwal-error-container').removeClass('hidden');
                    } else {
                        Swal.fire('Gagal!', err.responseJSON?.message || 'Error', 'error');
                    }
                }
            });
        });
    });

    function loadRuanganByGedung(gedungId) {
        const ruanganSelect = $('#jadwal-ruangan-id');
        ruanganSelect.empty().append('<option value="">-- Muat Ruangan --</option>');

        if (!gedungId) {
            ruanganSelect.empty().append('<option value="">-- Pilih Gedung Terlebih Dahulu --</option>');
            return $.Deferred().resolve().promise();
        }

        return $.get(`/api/ruangan?gedung_id=${gedungId}&per_page=500`, function(res) {
            const rooms = res.data.data || [];
            ruanganSelect.empty().append('<option value="">-- Pilih Ruangan --</option>');
            rooms.forEach(r => {
                ruanganSelect.append(`<option value="${r.id}">Lantai ${r.lantai} - ${r.nama}</option>`);
            });
        }).fail(() => {
            ruanganSelect.empty().append('<option value="">-- Gagal memuat --</option>');
        });
    }

    function loadKelasParalelOptions(mkId) {
        const kelasSelect = $('#jadwal-kelas-paralel-id');
        kelasSelect.empty().append('<option value="">-- Muat Kelas --</option>');

        if (!mkId) {
            kelasSelect.empty().append('<option value="">-- Pilih Mata Kuliah Terlebih Dahulu --</option>');
            return $.Deferred().resolve().promise();
        }

        return $.get(`/api/kelas-paralel?mata_kuliah_id=${mkId}`, function(res) {
            const data = res.data?.data || [];
            kelasSelect.empty().append('<option value="">-- Pilih Kelas --</option>');
            data.forEach(k => {
                const opt = document.createElement('option');
                opt.value = k.id;
                opt.textContent = k.sudah_terjadwal ? `${k.nama_kelas} (Sudah Terjadwal)` : k.nama_kelas;
                if (k.sudah_terjadwal) opt.disabled = true;
                kelasSelect.append(opt);
            });
        }).fail(() => {
            kelasSelect.empty().append('<option value="">-- Gagal memuat kelas --</option>');
        });
    }

    function openJadwalModal(mode, id = null) {
        jadwalSubmitMode = mode;
        $('#jadwal-form')[0].reset();
        $('#jadwal-id').val('');
        $('#jadwal-error-container').addClass('hidden');
        $('#jadwal-modal-title').text(mode === 'add' ? 'Tambah Jadwal' : 'Edit Jadwal');

        Promise.all([loadMatkulOptions(), loadGedungOptions()]).then(() => {
            if(mode === 'edit') {
                $.get(`/api/jadwal-mata-kuliah/${id}`, async (res) => {
                    const data = res.data;
                    $('#jadwal-id').val(data.id);
                    $('#jadwal-mata-kuliah-id').val(data.mata_kuliah_id);
                    loadKelasParalelOptions(data.mata_kuliah_id).then(() => {
                        $('#jadwal-kelas-paralel-id').val(data.kelas_paralel_id);
                    });
                    $('#jadwal-hari').val(data.hari);
                    $('#jadwal-jam-mulai').val(data.jam_mulai);
                    $('#jadwal-jam-selesai').val(data.jam_selesai);
                    $('#jadwal-gedung-id').val(data.gedung_id);
                    loadRuanganByGedung(data.gedung_id).then(() => {
                        if (data.ruangan_id) {
                            $('#jadwal-ruangan-id').val(data.ruangan_id);
                        }
                    });
                });
            }
        });
        $('#jadwal-modal').removeClass('hidden').addClass('flex');
        setTimeout(() => $('#jadwal-modal-content').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    }

    let gedungOptionsLoaded = false;
    function loadGedungOptions() {
        return new Promise((resolve) => {
            if (gedungOptionsLoaded) return resolve();
            $.get('/api/gedung?per_page=500', function(res) {
                const select = $('#jadwal-gedung-id');
                select.empty().append('<option value="">-- Pilih Gedung --</option>');
                res.data.data.forEach(g => {
                    select.append(`<option value="${g.id}">${g.nama} (${g.kode})</option>`);
                });
                gedungOptionsLoaded = true;
                resolve();
            }).fail(() => {
                showNotification('Error', 'Gagal memuat daftar gedung', 'error');
                resolve();
            });
        });
    }

    function closeJadwalModal() {
        $('#jadwal-modal-content').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#jadwal-modal').addClass('hidden').removeClass('flex'), 300);
    }

    let matkulOptionsLoaded = false;
    function loadMatkulOptions() {
        return new Promise((resolve) => {
            if (matkulOptionsLoaded) return resolve();
            $.get('/api/mata-kuliah?per_page=500', function(res) {
                const select = $('#jadwal-mata-kuliah-id');
                select.empty().append('<option value="">-- Pilih Mata Kuliah --</option>');
                res.data.data.forEach(mk => {
                    const opt = document.createElement('option');
                    opt.value = mk.id;
                    opt.textContent = mk.sudah_terjadwal ? `${mk.nama} (Sudah Terjadwal)` : mk.nama;
                    if (mk.sudah_terjadwal) opt.disabled = true;
                    select.append(opt);
                });
                matkulOptionsLoaded = true;
                resolve();
            }).fail(() => {
                showNotification('Error', 'Gagal memuat daftar mata kuliah', 'error');
                resolve();
            });
        });
    }

    function deleteJadwal(id) {
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: 'Jadwal kuliah akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/api/jadwal-mata-kuliah/${id}`,
                    type: 'DELETE',
                    success: (res) => {
                        jadwalTable.ajax.reload();
                        showNotification('Terhapus', res.message);
                    }
                });
            }
        });
    }
</script>
@endpush