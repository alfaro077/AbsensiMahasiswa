<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi Mahasiswa')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN because Vite requires Node 20+) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <!-- DataTables TailwindCSS Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- QR Code & Scanner Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

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
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col hidden" id="app-body">

    <!-- Top Navigation Bar -->
    <nav class="bg-indigo-600 text-white shadow-lg sticky top-0 z-30 print:hidden">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Hamburger + Brand -->
                <div class="flex items-center gap-4">
                    <button id="sidebar-toggle" class="p-2 rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none">
                        <svg id="menu-icon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg id="close-icon" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <a href="/" class="flex items-center gap-2 group">
                        <div class="bg-white/20 p-1.5 rounded-lg group-hover:bg-white/30 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight">PresensiApp</span>
                    </a>
                </div>

                <!-- Right: User Info + Logout -->
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end">
                        <span id="nav-user-name" class="text-xs font-semibold uppercase tracking-wider text-indigo-200"></span>
                        <span id="nav-user-role" class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded text-white uppercase font-bold"></span>
                    </div>
                    
                    <div class="h-8 w-px bg-indigo-500 hidden sm:block"></div>

                    <button onclick="logout()" class="flex text-sm bg-indigo-700 hover:bg-red-500 px-4 py-2 rounded-lg font-semibold transition-all duration-300 shadow-sm items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Layout: Sidebar + Content -->
    <div class="flex flex-1 overflow-hidden">

        <!-- Sidebar Backdrop (mobile) -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300 md:hidden print:hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col md:z-20 print:hidden">
            <!-- Sidebar Header -->
            <div class="p-6 border-b border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-1.5 rounded-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-bold text-lg">PresensiApp</span>
                </div>
                <button id="sidebar-close-btn" class="md:hidden p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-indigo-400 flex items-center justify-center text-lg font-bold text-white shadow-inner" id="sidebar-avatar">?</div>
                    <div>
                        <div id="sidebar-user-name" class="font-bold text-white leading-tight text-sm"></div>
                        <div id="sidebar-user-role" class="text-[10px] text-indigo-300 uppercase font-black tracking-widest"></div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1" id="sidebar-links">
                <!-- Injected by JavaScript based on Role -->
            </nav>

        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 flex flex-col overflow-hidden transition-all duration-300 md:ml-64">
            <div class="flex-grow overflow-y-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 w-full">
                <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-slate-500">
                    &copy; {{ date('Y') }} Sistem Presensi Mahasiswa. All rights reserved.
                </div>
            </footer>
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables Core & Tailwind JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Global AJAX & Auth Setup -->
    <script>
        $(document).ready(function() {
            const token = localStorage.getItem('token');
            const userStr = localStorage.getItem('user');

            if (!token || !userStr) {
                window.location.href = '/login';
                return;
            }

            document.getElementById('app-body').classList.remove('hidden');
            
            const user = JSON.parse(userStr);
            
            // Set User Info - Navbar
            $('#nav-user-name').text('Hai, ' + user.nama);
            $('#nav-user-role').text(user.role);
            
            // Set User Info - Sidebar
            $('#sidebar-user-name').text(user.nama);
            $('#sidebar-user-role').text(user.role);
            $('#sidebar-avatar').text(user.nama.charAt(0).toUpperCase());

            const path = window.location.pathname;
            const isDesktop = window.innerWidth >= 768;

            // Sidebar state management
            const savedState = localStorage.getItem('sidebarCollapsed');

            // Desktop: restore saved state (default: open)
            if (isDesktop) {
                if (savedState === 'true') {
                    $('#sidebar').addClass('-translate-x-full');
                    $('#main-content').removeClass('md:ml-64');
                    $('#menu-icon').removeClass('hidden');
                    $('#close-icon').addClass('hidden');
                } else {
                    $('#sidebar').removeClass('-translate-x-full');
                    $('#main-content').addClass('md:ml-64');
                    $('#menu-icon').addClass('hidden');
                    $('#close-icon').removeClass('hidden');
                }
            } else {
                // Mobile: always closed by default
                $('#sidebar').addClass('-translate-x-full');
                $('#main-content').removeClass('md:ml-64');
                $('#menu-icon').removeClass('hidden');
                $('#close-icon').addClass('hidden');
            }

            // Render Navigation Links based on Role
            const sidebarNav = $('#sidebar-links');
            
            const linkClass = "flex items-center gap-3 w-full px-4 py-3 rounded-lg text-sm font-semibold transition-all";
            const activeClass = "bg-indigo-600 text-white shadow-md";
            const normalClass = "text-slate-300 hover:bg-slate-800 hover:text-white";

            let links = [];

            if (user.role === 'admin') {
                links = [
                    { name: 'Mahasiswa', url: '/mahasiswa', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>' },
                    { name: 'Dosen', url: '/dosen', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' },
                    { name: 'Jurusan', url: '/jurusan', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>' },
                    { name: 'Gedung', url: '/gedung', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>' },
                    { name: 'Ruangan', url: '/ruangan', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a1 1 0 011 1v1h4a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2h4V3a1 1 0 011-1zm-1 4H7v12h10V6h-4v1a1 1 0 11-2 0V6z"></path></svg>' },
                    { name: 'Jadwal Kuliah', url: '/jadwal', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>' },
                    { name: 'Mata Kuliah', url: '/mata-kuliah', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>' },
                    { name: 'Laporan', url: '/laporan', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' },
                    { name: 'Profil Saya', url: '/profile', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' }
                ];
            } else if (user.role === 'dosen') {
                links = [
                    { name: 'Dashboard', url: '/dashboard-dosen', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>' },
                    { name: 'Jadwal Kuliah', url: '/jadwal', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>' },
                    { name: 'Mata Kuliah', url: '/mata-kuliah', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>' },
                    { name: 'Profil Saya', url: '/profile', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' }
                ];
            } else if (user.role === 'mahasiswa') {
                links = [
                    { name: 'Beranda', url: '/dashboard-mahasiswa', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>' },
                    { name: 'Jadwal Kuliah', url: '/jadwal', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>' },
                    { name: 'Mata Kuliah', url: '/mata-kuliah', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>' },
                    { name: 'Profil Saya', url: '/profile', icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' }
                ];
            }

            // Render sidebar for ALL roles
            links.forEach(link => {
                const isActive = path === link.url || path.startsWith(link.url + '/');
                sidebarNav.append(`<a href="${link.url}" class="${linkClass} ${isActive ? activeClass : normalClass}">${link.icon} ${link.name}</a>`);
            });

            // Sidebar Toggle Function
            function toggleSidebar() {
                const isOpen = !$('#sidebar').hasClass('-translate-x-full');
                const desktop = window.innerWidth >= 768;

                if (isOpen) {
                    // Close sidebar
                    $('#sidebar').addClass('-translate-x-full');
                    $('#sidebar-backdrop').addClass('hidden');
                    if (desktop) {
                        $('#main-content').removeClass('md:ml-64');
                        localStorage.setItem('sidebarCollapsed', 'true');
                    }
                    // Show hamburger, hide X
                    $('#menu-icon').removeClass('hidden');
                    $('#close-icon').addClass('hidden');
                } else {
                    // Open sidebar
                    $('#sidebar').removeClass('-translate-x-full');
                    if (!desktop) {
                        $('#sidebar-backdrop').removeClass('hidden');
                    }
                    if (desktop) {
                        $('#main-content').addClass('md:ml-64');
                        localStorage.setItem('sidebarCollapsed', 'false');
                    }
                    // Show X, hide hamburger
                    $('#menu-icon').addClass('hidden');
                    $('#close-icon').removeClass('hidden');
                }
            }

            // Sidebar toggle events
            $('#sidebar-toggle').on('click', toggleSidebar);
            $('#sidebar-close-btn').on('click', toggleSidebar);
            $('#sidebar-backdrop').on('click', toggleSidebar);

            // Close sidebar on link click (mobile)
            $(document).on('click', '#sidebar-links a', function() {
                if (window.innerWidth < 768) {
                    $('#sidebar').addClass('-translate-x-full');
                    $('#sidebar-backdrop').addClass('hidden');
                    $('#menu-icon').removeClass('hidden');
                    $('#close-icon').addClass('hidden');
                }
            });

            // Handle window resize
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    const width = window.innerWidth;
                    const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';

                    if (width >= 768) {
                        // Desktop: hide backdrop, restore saved state
                        $('#sidebar-backdrop').addClass('hidden');
                        if (collapsed) {
                            $('#sidebar').addClass('-translate-x-full');
                            $('#main-content').removeClass('md:ml-64');
                            $('#menu-icon').removeClass('hidden');
                            $('#close-icon').addClass('hidden');
                        } else {
                            $('#sidebar').removeClass('-translate-x-full');
                            $('#main-content').addClass('md:ml-64');
                            $('#menu-icon').addClass('hidden');
                            $('#close-icon').removeClass('hidden');
                        }
                    } else {
                        // Mobile: close sidebar
                        $('#sidebar').addClass('-translate-x-full');
                        $('#sidebar-backdrop').addClass('hidden');
                        $('#main-content').removeClass('md:ml-64');
                        $('#menu-icon').removeClass('hidden');
                        $('#close-icon').addClass('hidden');
                    }
                }, 100);
            });

            $.ajaxSetup({
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            });
        });

        function showNotification(title, message, icon = 'success') {
            Swal.fire({
                title: title,
                text: message,
                icon: icon,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    </script>

    @stack('scripts')
</body>
</html>
