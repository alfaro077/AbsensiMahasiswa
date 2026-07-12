<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/dashboard-dosen', function () {
    return view('dashboard.dosen');
});

Route::get('/dashboard-mahasiswa', function () {
    return view('dashboard.mahasiswa');
});

Route::get('/mahasiswa', function () {
    return view('mahasiswa.index');
});

Route::get('/dosen', function () {
    return view('dosen.index');
});

Route::get('/mata-kuliah', function () {
    return view('mata_kuliah.index');
});

Route::get('/gedung', function () {
    return view('gedung.index');
});

Route::get('/ruangan', function () {
    return view('ruangan.index');
});

Route::get('/jadwal', function () {
    return view('jadwal.index');
});

Route::get('/jurusan', function () {
    return view('jurusan.index');
});

Route::get('/profile', function () {
    return view('profile.index');
});

Route::get('/laporan', function () {
    return view('laporan.index');
});

Route::get('/telegram-test', function () {
    return view('telegram-test.index');
});
