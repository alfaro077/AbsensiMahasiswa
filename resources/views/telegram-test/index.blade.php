@extends('layouts.app')
@section('title', 'Telegram Test')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Telegram Testing</h1>
            <p class="text-slate-500 text-sm mt-1">Uji coba notifikasi Telegram & status bot.</p>
        </div>
    </div>

    <!-- Status Bot -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500" id="status-dot"></span>
            Status Bot Telegram
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="bot-status-container">
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Token</p>
                <p id="status-token" class="text-lg font-bold text-slate-700 mt-1">Memuat...</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Bot Username</p>
                <p id="status-username" class="text-lg font-bold text-slate-700 mt-1">Memuat...</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Webhook</p>
                <p id="status-webhook" class="text-lg font-bold text-slate-700 mt-1">Memuat...</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                <p class="text-xs text-slate-500 uppercase font-semibold tracking-wider">Koneksi</p>
                <p id="status-valid" class="text-lg font-bold text-slate-700 mt-1">Memuat...</p>
            </div>
        </div>
    </div>

    <!-- Kirim Pesan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pesan Kustom -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Kirim Pesan Kustom</h2>
            <form id="form-custom" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">User Tujuan</label>
                    <select id="custom-user-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Muat User --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pesan</label>
                    <textarea id="custom-message" rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Tulis pesan Anda di sini..."></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm">
                    Kirim Pesan
                </button>
                <div id="custom-result" class="hidden p-3 rounded-lg text-sm font-medium"></div>
            </form>
        </div>

        <!-- Simulasi Jadwal -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Kirim Simulasi Jadwal</h2>
            <form id="form-schedule" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">User Tujuan</label>
                    <select id="schedule-user-id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">-- Muat User --</option>
                    </select>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    <p class="font-semibold mb-1">Info</p>
                    <p>Notifikasi jadwal <strong>hari ini</strong> akan dikirim ke user tujuan dengan format yang sama seperti notifikasi asli.</p>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all shadow-sm">
                    Kirim Simulasi Jadwal
                </button>
                <div id="schedule-result" class="hidden p-3 rounded-lg text-sm font-medium"></div>
            </form>
        </div>
    </div>

    <!-- User Terhubung -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800">User Terhubung Telegram</h2>
            <p class="text-slate-500 text-sm mt-1">Daftar user yang sudah menghubungkan akun Telegram.</p>
        </div>
        <div class="p-0 sm:p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table id="users-table" class="w-full text-left text-sm border-collapse">
                    <thead class="bg-indigo-50/50 text-indigo-700 font-bold border-b border-indigo-100">
                        <tr>
                            <th class="p-4 uppercase tracking-wider text-[11px]">Nama</th>
                            <th class="p-4 uppercase tracking-wider text-[11px]">Email</th>
                            <th class="p-4 uppercase tracking-wider text-[11px]">Role</th>
                            <th class="p-4 uppercase tracking-wider text-[11px]">Chat ID</th>
                            <th class="p-4 text-right uppercase tracking-wider text-[11px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        loadBotStatus();
        loadConnectedUsers();

        // Load users for both dropdowns
        loadUserDropdowns();

        // Kirim Pesan Kustom
        $('#form-custom').on('submit', function(e) {
            e.preventDefault();
            const userId = $('#custom-user-id').val();
            const message = $('#custom-message').val();
            const resultDiv = $('#custom-result');

            if (!userId) { showResult(resultDiv, 'Pilih user terlebih dahulu.', 'error'); return; }
            if (!message.trim()) { showResult(resultDiv, 'Pesan tidak boleh kosong.', 'error'); return; }

            $('#form-custom button[type="submit"]').prop('disabled', true).text('Mengirim...');
            resultDiv.addClass('hidden');

            $.ajax({
                url: '/api/telegram/test-send',
                type: 'POST',
                data: JSON.stringify({ user_id: parseInt(userId), type: 'custom', message: message }),
                contentType: 'application/json',
                success: (res) => showResult(resultDiv, res.message, 'success'),
                error: (err) => showResult(resultDiv, err.responseJSON?.message || 'Gagal mengirim.', 'error'),
                complete: () => $('#form-custom button[type="submit"]').prop('disabled', false).text('Kirim Pesan'),
            });
        });

        // Kirim Simulasi Jadwal
        $('#form-schedule').on('submit', function(e) {
            e.preventDefault();
            const userId = $('#schedule-user-id').val();
            const resultDiv = $('#schedule-result');

            if (!userId) { showResult(resultDiv, 'Pilih user terlebih dahulu.', 'error'); return; }

            $('#form-schedule button[type="submit"]').prop('disabled', true).text('Mengirim...');
            resultDiv.addClass('hidden');

            $.ajax({
                url: '/api/telegram/test-send',
                type: 'POST',
                data: JSON.stringify({ user_id: parseInt(userId), type: 'schedule' }),
                contentType: 'application/json',
                success: (res) => showResult(resultDiv, res.message, 'success'),
                error: (err) => showResult(resultDiv, err.responseJSON?.message || 'Gagal mengirim.', 'error'),
                complete: () => $('#form-schedule button[type="submit"]').prop('disabled', false).text('Kirim Simulasi Jadwal'),
            });
        });
    });

    function loadBotStatus() {
        $.get('/api/telegram/bot-status', function(res) {
            const d = res.data;

            $('#status-token').html(d.has_token
                ? '<span class="text-emerald-600">Terisi ✅</span>'
                : '<span class="text-red-500">Belum diisi ❌</span>');
            $('#status-username').text(d.bot_username || '—');
            $('#status-webhook').text(d.webhook?.result?.url
                ? d.webhook.result.url.replace(/^https?:\/\//, '').substring(0, 35) + '...'
                : 'Belum diatur');
            $('#status-valid').html(d.valid
                ? '<span class="text-emerald-600">Valid ✅</span>'
                : '<span class="text-red-500">Tidak valid ❌</span>');
            $('#status-dot').removeClass('bg-red-500 bg-gray-400').addClass(d.valid ? 'bg-emerald-500' : 'bg-red-500');
        }).fail(() => {
            $('#status-token').text('Gagal memuat');
            $('#status-username').text('—');
            $('#status-webhook').text('—');
            $('#status-valid').text('Error');
            $('#status-dot').removeClass('bg-emerald-500').addClass('bg-red-500');
        });
    }

    function loadConnectedUsers() {
        $('#users-table').DataTable({
            ajax: { url: '/api/telegram/connected-users', dataSrc: 'data' },
            columns: [
                { data: 'nama', className: 'p-4 font-semibold text-slate-800' },
                { data: 'email', className: 'p-4 text-slate-600' },
                { data: 'role', className: 'p-4', render: (d) => `<span class="px-2 py-1 rounded text-xs font-bold uppercase ${d === 'admin' ? 'bg-purple-100 text-purple-700' : d === 'dosen' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'}">${d}</span>` },
                { data: 'telegram_chat_id', className: 'p-4 text-slate-500 font-mono text-xs' },
                {
                    data: null, className: 'p-4 text-right', orderable: false,
                    render: (data) => `
                        <button onclick="quickSend('${data.id}', '${data.nama}')" class="inline-flex items-center gap-1 text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white px-2 py-1.5 rounded-lg transition-all text-xs font-bold shadow-sm">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            KIRIM PESAN
                        </button>
                    `
                }
            ],
            language: {
                search: "", searchPlaceholder: "Cari user...",
                lengthMenu: "_MENU_",
                emptyTable: "Belum ada user yang menghubungkan Telegram."
            },
            drawCallback: function() {
                $('.dataTables_paginate').addClass('flex justify-end gap-1 mt-4 p-4');
                $('.dataTables_info').addClass('text-xs text-slate-400 p-4');
            }
        });
    }

    function loadUserDropdowns() {
        $.get('/api/telegram/connected-users', function(res) {
            const users = res.data || [];
            ['custom-user-id', 'schedule-user-id'].forEach(id => {
                const select = $('#' + id);
                select.empty().append('<option value="">-- Pilih User --</option>');
                users.forEach(u => {
                    select.append(`<option value="${u.id}">${u.nama} (${u.role})</option>`);
                });
            });
        }).fail(() => {
            ['custom-user-id', 'schedule-user-id'].forEach(id => {
                $('#' + id).empty().append('<option value="">-- Gagal memuat --</option>');
            });
        });
    }

    function showResult(el, message, type) {
        el.removeClass('hidden bg-emerald-50 text-emerald-700 border border-emerald-200 bg-red-50 text-red-700 border border-red-200')
          .addClass(type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200')
          .text(message);
    }

    function quickSend(userId, userName) {
        $('#custom-user-id').val(userId);
        $('#custom-message').val(`Halo ${userName}! Ini adalah pesan uji coba dari Admin.`);
        $('#custom-result').addClass('hidden');
        $('html, body').animate({ scrollTop: $('#form-custom').offset().top - 120 }, 500);
        setTimeout(() => $('#custom-message').focus(), 500);
    }
</script>
@endpush