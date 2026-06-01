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
</script>
@endpush
