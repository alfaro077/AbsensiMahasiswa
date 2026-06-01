<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Presensi Mahasiswa</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-indigo-50: #f0f6fd;
            --color-indigo-100: #e1edf9;
            --color-indigo-200: #badaf5;
            --color-indigo-300: #9ac1f0;
            --color-indigo-400: #74a7eb;
            --color-indigo-500: #4c89df;
            --color-indigo-600: #2c6bca;
            --color-indigo-700: #2254a4;
            --color-indigo-800: #1f4585;
            --color-indigo-900: #1c3a6f;
            --color-indigo-950: #122447;

            --color-emerald-50: #effdf5;
            --color-emerald-100: #dbfbe6;
            --color-emerald-200: #b7f7cd;
            --color-emerald-300: #72fa93;
            --color-emerald-400: #4be772;
            --color-emerald-500: #21c44b;
            --color-emerald-600: #12a63a;
            --color-emerald-700: #128231;
            --color-emerald-800: #13672a;
            --color-emerald-900: #125425;
            --color-emerald-950: #053013;

            --color-lime-50: #f7fdf0;
            --color-lime-100: #ecfae0;
            --color-lime-200: #dcf7c0;
            --color-lime-300: #c1f092;
            --color-lime-400: #a0e548;
            --color-lime-500: #82cc2b;
            --color-lime-600: #63a31c;
            --color-lime-700: #4b7e17;
            --color-lime-800: #3f6618;
            --color-lime-950: #1a2f05;

            --color-rose-50: #fef5f2;
            --color-rose-100: #fde8e1;
            --color-rose-200: #ffd1c2;
            --color-rose-300: #ffa890;
            --color-rose-400: #fd7855;
            --color-rose-500: #e45f2b;
            --color-rose-600: #db4b19;
            --color-rose-700: #b83a12;
            --color-rose-800: #983213;
            --color-rose-900: #7e2d14;
            --color-rose-950: #451305;

            --color-amber-50: #fefbf2;
            --color-amber-100: #fdf5d9;
            --color-amber-200: #fae9af;
            --color-amber-300: #f8db7b;
            --color-amber-400: #f6c445;
            --color-amber-500: #e0ab2a;
            --color-amber-600: #c1881f;
            --color-amber-700: #9c631c;
            --color-amber-800: #81501c;
            --color-amber-900: #6b411c;
            --color-amber-950: #3f210a;
        }

        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient Radial Glow Circles for Elegance -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-indigo-200/40 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-100/50 rounded-full blur-3xl translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10">
        <div class="bg-white border border-slate-100/80 rounded-3xl p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.03)] relative overflow-hidden">
            
            <div class="text-center mb-8 relative">
                <div class="inline-flex p-3 rounded-2xl bg-indigo-50 border border-indigo-100/70 mb-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Presensi<span class="text-indigo-500">App</span></h1>
                <p class="text-slate-400 mt-2 text-xs font-semibold uppercase tracking-wider">Sistem Presensi Mahasiswa Cerdas</p>
            </div>
    
            <form id="login-form" class="space-y-5 relative">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Email Institusi</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input type="email" id="email" required class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 placeholder-slate-400/80 pl-12 pr-4 py-3.5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 focus:bg-white transition-all duration-300 text-sm font-medium shadow-inner" placeholder="nama@mhs.example.com">
                    </div>
                </div>
    
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Kata Sandi</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password" id="password" required class="w-full bg-slate-50/70 border border-slate-200 text-slate-800 placeholder-slate-400/80 pl-12 pr-12 py-3.5 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 focus:bg-white transition-all duration-300 text-sm font-medium shadow-inner" placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                            <svg id="eye-open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg id="eye-closed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>

                <script>
                    function togglePassword() {
                        const passInput = document.getElementById('password');
                        const eyeOpen = document.getElementById('eye-open');
                        const eyeClosed = document.getElementById('eye-closed');
                        
                        if (passInput.type === 'password') {
                            passInput.type = 'text';
                            eyeOpen.classList.remove('hidden');
                            eyeClosed.classList.add('hidden');
                        } else {
                            passInput.type = 'password';
                            eyeOpen.classList.add('hidden');
                            eyeClosed.classList.remove('hidden');
                        }
                    }
                </script>
    
                <div class="flex items-center justify-between text-xs text-slate-500 px-1 py-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" id="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500/30 transition-all">
                        <span class="group-hover:text-slate-800 transition-colors">Tetap Masuk</span>
                    </label>
                    <a href="#" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline transition-all">Lupa Password?</a>
                </div>
    
                <button type="submit" id="btn-login" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl hover:scale-[1.01] active:scale-[0.99] transition-all duration-300 shadow-[0_10px_25px_rgba(44,107,202,0.15)] hover:shadow-[0_12px_30px_rgba(44,107,202,0.25)] uppercase tracking-wider flex items-center justify-center gap-2 text-xs cursor-pointer">
                    Masuk Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </button>
            </form>
        </div>
        <p class="mt-8 text-center text-slate-400 text-xs font-medium tracking-wider">
            &copy; 2026 Presensi App System. All rights reserved.
        </p>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Check if already logged in
            const token = localStorage.getItem('token');
            if(token) {
                redirectDashboard(JSON.parse(localStorage.getItem('user')));
            }

            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                $('#btn-login').prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: '/api/login',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email: $('#email').val(),
                        password: $('#password').val()
                    }),
                    success: function(res) {
                        // Res: { success: true, message: ..., access_token: ..., user: ... }
                        localStorage.setItem('token', res.access_token);
                        localStorage.setItem('user', JSON.stringify(res.user));

                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil',
                            text: 'Mengarahkan ke Dashboard...',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            redirectDashboard(res.user);
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON?.message || 'Login gagal!';
                        Swal.fire('Error', msg, 'error');
                        $('#btn-login').prop('disabled', false).text('Masuk');
                    }
                });
            });
        });

        function redirectDashboard(user) {
            if(!user) return;
            if(user.role === 'dosen') {
                window.location.href = '/dashboard-dosen';
            } else if(user.role === 'mahasiswa') {
                window.location.href = '/dashboard-mahasiswa';
            } else {
                // Fallback validasi master data
                window.location.href = '/mahasiswa';
            }
        }
    </script>
</body>
</html>
