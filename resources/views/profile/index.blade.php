@extends('layouts.app')

@section('title', 'Profil Saya - PresensiApp')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-800">Profil Saya</h1>
        <p class="text-sm text-slate-500">Kelola informasi pribadi dan pengaturan keamanan akun Anda.</p>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Profile Summary Card (Read-Only Academic Info) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col items-center text-center relative overflow-hidden">
                <!-- Decorative top gradient bar -->
                <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-indigo-500 to-emerald-500"></div>

                <!-- Avatar Inisial -->
                <div id="profile-avatar" class="h-24 w-24 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 text-white flex items-center justify-center text-4xl font-extrabold shadow-lg shadow-indigo-100 mb-4 mt-2 select-none border-4 border-white">
                    ?
                </div>

                <h3 id="profile-name" class="font-bold text-slate-800 text-xl leading-tight"></h3>
                <span id="profile-role" class="mt-2 text-xs bg-indigo-50 text-indigo-700 border border-indigo-100 font-bold px-3 py-1 rounded-full uppercase tracking-wider"></span>
                
                <div class="h-px bg-slate-100 w-full my-6"></div>

                <!-- Academic Info List -->
                <div class="w-full space-y-4 text-left">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Informasi Akademik</h4>
                    
                    <div id="academic-fields" class="space-y-3">
                        <!-- Filled by JavaScript -->
                        <div class="animate-pulse space-y-2">
                            <div class="h-4 bg-slate-100 rounded w-1/2"></div>
                            <div class="h-4 bg-slate-100 rounded w-3/4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Edit Form Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-1 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan Akun
                </h3>
                <p class="text-sm text-slate-500 mb-6">Perbarui informasi kredensial utama akun Anda.</p>

                <form id="profile-form" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" required class="w-full rounded-lg border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm text-sm" placeholder="Nama Lengkap Anda">
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-slate-700">Alamat Email</label>
                            <input type="email" id="email" name="email" required class="w-full rounded-lg border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm text-sm" placeholder="email@domain.com">
                        </div>

                        <!-- Password Baru -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-slate-700">Kata Sandi Baru (Opsional)</label>
                            <input type="password" id="password" name="password" minlength="8" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm text-sm" placeholder="••••••••">
                            <p class="text-[10px] text-slate-400 mt-1 italic">*Biarkan kosong jika tidak ingin mengubah kata sandi.</p>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="space-y-1">
                            <label class="block text-sm font-semibold text-slate-700">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm text-sm" placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Telegram Bot Connection -->
                    <div class="h-px bg-slate-100 w-full my-6"></div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-1 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Notifikasi Telegram
                        </h3>
                        <p class="text-sm text-slate-500 mb-4">Hubungkan Telegram untuk menerima notifikasi jadwal setiap pagi pukul 06:00 WIB.</p>

                        <div id="telegram-status" class="mb-4">
                            <div class="animate-pulse space-y-2">
                                <div class="h-5 bg-slate-100 rounded w-1/3"></div>
                            </div>
                        </div>

                        <div id="telegram-actions" class="flex flex-wrap gap-3">
                            <button type="button" id="btn-telegram-link" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center gap-2 cursor-pointer text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                Hubungkan Telegram
                            </button>
                            <button type="button" id="btn-telegram-unlink" class="hidden bg-red-50 hover:bg-red-100 text-red-600 font-bold px-5 py-2.5 rounded-xl border border-red-200 transition-all flex items-center gap-2 cursor-pointer text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Putuskan Koneksi
                            </button>
                        </div>

                        <div id="telegram-link-result" class="mt-4 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <p class="text-sm font-semibold text-blue-800 mb-2">Klik tombol di bawah untuk menghubungkan:</p>
                                <a id="telegram-link-url" href="#" target="_blank" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-xl transition-all text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Buka Telegram
                                </a>
                                <p class="text-xs text-blue-600 mt-2">Atau salin link ini: <span id="telegram-link-copy" class="font-mono bg-blue-100 px-2 py-1 rounded text-xs break-all"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100 w-full my-6"></div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" id="btn-save" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fetch Profile Details
        loadProfileData();

        // Handle Form Submit
        $('#profile-form').on('submit', function(e) {
            e.preventDefault();

            // Validate Passwords match if provided
            const pass = $('#password').val();
            const passConf = $('#password_confirmation').val();

            if (pass && pass !== passConf) {
                Swal.fire({
                    title: 'Kesalahan Konfirmasi',
                    text: 'Kata sandi baru dan konfirmasi kata sandi tidak cocok!',
                    icon: 'error',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }

            const formData = {
                nama: $('#nama').val(),
                email: $('#email').val()
            };

            if (pass) {
                formData.password = pass;
            }

            // Button Loading State
            const btn = $('#btn-save');
            const originalContent = btn.html();
            btn.prop('disabled', true).addClass('opacity-50').html(`
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `);

            $.ajax({
                url: '/api/profile',
                type: 'PUT',
                data: formData,
                success: function(res) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Profil Anda telah sukses diperbarui.',
                        icon: 'success',
                        confirmButtonColor: '#4f46e5'
                    });

                    // Update Local Storage
                    const user = JSON.parse(localStorage.getItem('user'));
                    user.nama = res.data.nama;
                    user.email = res.data.email;
                    localStorage.setItem('user', JSON.stringify(user));

                    // Update Header UI dynamically
                    $('#nav-user-name').text('Hai, ' + user.nama);
                    $('#nav-user-name-mobile').text(user.nama);
                    $('#nav-mobile-avatar').text(user.nama.charAt(0).toUpperCase());

                    // Reload Profile Card view
                    loadProfileData();

                    // Clear password fields
                    $('#password').val('');
                    $('#password_confirmation').val('');
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    let message = xhr.responseJSON?.message || 'Terjadi kesalahan sistem.';

                    if (errors) {
                        message = Object.values(errors).map(err => err.join('<br>')).join('<br>');
                    }

                    Swal.fire({
                        title: 'Gagal Memperbarui',
                        html: message,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).removeClass('opacity-50').html(originalContent);
                }
            });
        });
    });

    function loadProfileData() {
        $.get('/api/profile', function(res) {
            const user = res.data;

            // Fill Form Inputs
            $('#nama').val(user.nama);
            $('#email').val(user.email);

            // Fill Profile Card Main
            $('#profile-avatar').text(user.nama.charAt(0).toUpperCase());
            $('#profile-name').text(user.nama);
            $('#profile-role').text(user.role);

            // Academic fields dynamic rendering based on Role
            const fieldsContainer = $('#academic-fields');
            fieldsContainer.empty();

            if (user.role === 'mahasiswa' && user.mahasiswa) {
                const mhs = user.mahasiswa;
                const jurusan = mhs.jurusan?.nama || 'N/A';
                fieldsContainer.append(`
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">NIM</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">${mhs.nim}</span>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">Program Studi / Jurusan</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">${jurusan}</span>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">Angkatan</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">Tahun ${mhs.angkatan}</span>
                    </div>
                `);
            } else if (user.role === 'dosen' && user.dosen) {
                const dsn = user.dosen;
                const jurusan = dsn.jurusan?.nama || 'N/A';
                fieldsContainer.append(`
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">NIP / Kode Dosen</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">${dsn.nip}</span>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">Jabatan / Fungsional</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">${dsn.jabatan || 'Dosen Pengampu'}</span>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">Homebase Jurusan</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">${jurusan}</span>
                    </div>
                `);
            } else if (user.role === 'admin') {
                fieldsContainer.append(`
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">Akses Akun</label>
                        <span class="text-sm font-semibold text-emerald-600 flex items-center gap-1 block mt-0.5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a.75.75 0 00-.708.522L3.547 10.22a.75.75 0 00.3.8l5.25 3.5a.75.75 0 00.83 0l5.25-3.5a.75.75 0 00.3-.8L13.44 3.977a.75.75 0 00-.708-.522H6.267zm2.4 8.78l-3.32-2.213.9-2.7 5.506 3.671-3.086 1.242z" clip-rule="evenodd"></path></svg>
                            Super Administrator
                        </span>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400">Sistem</label>
                        <span class="text-sm font-semibold text-slate-700 block mt-0.5">Semua Hak Akses Aktif</span>
                    </div>
                `);
            }
        });
    }

    // ─── Telegram Integration ──────────────────────────────
    function loadTelegramStatus() {
        $.get('/api/telegram/status', function(res) {
            const connected = res.data.connected;
            const chatId = res.data.chat_id;
            const nama = res.data.nama;

            const statusEl = $('#telegram-status');
            const linkBtn = $('#btn-telegram-link');
            const unlinkBtn = $('#btn-telegram-unlink');
            const linkResult = $('#telegram-link-result');

            if (connected) {
                statusEl.html(`
                    <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold">Terhubung sebagai <span class="font-bold">${nama}</span> (Chat ID: ${chatId})</span>
                    </div>
                `);
                linkBtn.hide();
                unlinkBtn.removeClass('hidden');
                linkResult.addClass('hidden');
            } else {
                statusEl.html(`
                    <div class="flex items-center gap-2 text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span class="text-sm">Akun Telegram belum terhubung</span>
                    </div>
                `);
                linkBtn.show();
                unlinkBtn.addClass('hidden');
                linkResult.addClass('hidden');
            }
        });
    }

    $(document).on('click', '#btn-telegram-link', function() {
        const btn = $(this);
        btn.prop('disabled', true).addClass('opacity-50').html(`
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `);

        $.post('/api/telegram/link', function(res) {
            if (res.success) {
                if (res.data.connected) {
                    loadTelegramStatus();
                    Swal.fire({
                        title: 'Sudah Terhubung',
                        text: 'Akun Telegram Anda sudah terhubung.',
                        icon: 'info',
                        confirmButtonColor: '#4f46e5'
                    });
                } else {
                    const link = res.data.link;
                    $('#telegram-link-url').attr('href', link);
                    $('#telegram-link-copy').text(link);
                    $('#telegram-link-result').removeClass('hidden');
                    btn.hide();
                }
            }
        }).fail(function(xhr) {
            const msg = xhr.responseJSON?.message || 'Gagal membuat link.';
            Swal.fire({
                title: 'Gagal',
                text: msg,
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        }).always(function() {
            btn.prop('disabled', false).removeClass('opacity-50').html(`
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Hubungkan Telegram
            `);
        });
    });

    $(document).on('click', '#btn-telegram-unlink', function() {
        Swal.fire({
            title: 'Putuskan Koneksi?',
            text: 'Anda tidak akan menerima notifikasi Telegram lagi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Putuskan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/api/telegram/unlink', function(res) {
                    if (res.success) {
                        loadTelegramStatus();
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Koneksi Telegram berhasil diputuskan.',
                            icon: 'success',
                            confirmButtonColor: '#4f46e5'
                        });
                    }
                });
            }
        });
    });

    // Load Telegram status after profile is loaded
    setTimeout(loadTelegramStatus, 100);
</script>
@endpush
