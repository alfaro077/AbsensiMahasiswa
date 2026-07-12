<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Gedung;
use App\Models\Ruangan;
use App\Models\JadwalMataKuliah;
use App\Models\KelasParalel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Jurusan::truncate();
        Dosen::truncate();
        Mahasiswa::truncate();
        MataKuliah::truncate();
        Gedung::truncate();
        Ruangan::truncate();
        JadwalMataKuliah::truncate();
        KelasParalel::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Admin
        User::create([
            'nama'     => 'Administrator',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // 2. Jurusan
        $ti = Jurusan::create(['kode' => 'TI', 'nama' => 'Teknik Informatika']);

        // 3. Dosen (2 orang)
        $userD1 = User::create([
            'nama'     => 'Budi Susanto, M.Kom',
            'email'    => 'dosen1@example.com',
            'password' => Hash::make('password'),
            'role'     => 'dosen',
        ]);
        $d1 = Dosen::create([
            'user_id'    => $userD1->id,
            'jurusan_id' => $ti->id,
            'nip'        => '198001012010011001',
            'jabatan'    => 'Lektor',
        ]);

        $userD2 = User::create([
            'nama'     => 'Siti Aminah, Ph.D.',
            'email'    => 'dosen2@example.com',
            'password' => Hash::make('password'),
            'role'     => 'dosen',
        ]);
        $d2 = Dosen::create([
            'user_id'    => $userD2->id,
            'jurusan_id' => $ti->id,
            'nip'        => '198505052015042002',
            'jabatan'    => 'Asisten Ahli',
        ]);

        // 4. Mahasiswa (5 orang)
        $mhsData = [
            ['Andi Wijaya',    '20230001'],
            ['Bambang Heru',   '20230002'],
            ['Cici Paramida',  '20230003'],
            ['Dedi Pratama',   '20230004'],
            ['Eka Safitri',    '20230005'],
        ];

        $mahasiswas = [];
        foreach ($mhsData as $data) {
            $user = User::create([
                'nama'     => $data[0],
                'email'    => strtolower(str_replace(' ', '.', $data[0])) . '@mhs.example.com',
                'password' => Hash::make('password'),
                'role'     => 'mahasiswa',
            ]);
            $mahasiswas[] = Mahasiswa::create([
                'user_id'    => $user->id,
                'jurusan_id' => $ti->id,
                'nim'        => $data[1],
                'angkatan'   => 2023,
            ]);
        }

        // 5. Mata Kuliah (5 MK, 2 dosen bergantian)
        $mkNames = [
            ['TI101', 'Algoritma dan Pemrograman I', 3, 1, $d1->id],
            ['TI102', 'Matematika Diskrit',          3, 1, $d1->id],
            ['TI103', 'Sistem Digital',               3, 1, $d2->id],
            ['TI104', 'Struktur Data',                3, 2, $d1->id],
            ['TI105', 'Basis Data',                   4, 2, $d2->id],
        ];

        $allMk = [];
        foreach ($mkNames as $mk) {
            $allMk[] = MataKuliah::create([
                'kode'       => $mk[0],
                'nama'       => $mk[1],
                'sks'        => $mk[2],
                'semester'   => $mk[3],
                'jurusan_id' => $ti->id,
                'dosen_id'   => $mk[4],
            ]);
        }

        // 6. Gedung + Ruangan
        $g1 = Gedung::create(['kode' => 'GD-A', 'nama' => 'Gedung A', 'lokasi' => 'Kampus Timur']);

        $ruanganList = [];
        foreach (['101', '102', '201', '202'] as $nama) {
            $ruanganList[] = Ruangan::create([
                'gedung_id' => $g1->id,
                'nama'      => $nama,
                'lantai'    => $nama[0],
                'kapasitas' => 35,
            ]);
        }

        // 7. Kelas Paralel (2 per MK: A, B)
        $kelasByMk = [];
        foreach ($allMk as $mk) {
            foreach (['A', 'B'] as $namaKelas) {
                $k = KelasParalel::create([
                    'mata_kuliah_id' => $mk->id,
                    'nama_kelas'     => $namaKelas,
                    'dosen_id'       => null,
                    'tahun_ajaran'   => '2025/2026',
                ]);
                $kelasByMk[$mk->kode][] = $k;
            }
        }

        // 8. Jadwal
        $r101 = $ruanganList[0]; // 101
        $r102 = $ruanganList[1]; // 102
        $r201 = $ruanganList[2]; // 201
        $r202 = $ruanganList[3]; // 202

        $jadwalDefs = [
            ['TI101', 'A', 'Senin',  '07:00', '08:40', $r101],
            ['TI101', 'B', 'Senin',  '09:00', '10:40', $r102],
            ['TI102', 'A', 'Senin',  '07:00', '08:40', $r201],
            ['TI103', 'A', 'Selasa', '07:00', '08:40', $r101],
            ['TI104', 'A', 'Rabu',   '07:00', '08:40', $r101],
            ['TI105', 'A', 'Kamis',  '07:00', '08:40', $r101],
            ['TI102', 'B', 'Jumat',  '07:00', '08:40', $r101],
            ['TI103', 'B', 'Jumat',  '09:00', '10:40', $r102],
        ];

        foreach ($jadwalDefs as $j) {
            $mk = MataKuliah::where('kode', $j[0])->first();
            $kelas = collect($kelasByMk[$j[0]] ?? [])->firstWhere('nama_kelas', $j[1]);
            if ($mk && $kelas && $j[4]) {
                DB::table('jadwal_mata_kuliah')->insert([
                    'mata_kuliah_id'   => $mk->id,
                    'kelas_paralel_id' => $kelas->id,
                    'hari'             => $j[2],
                    'jam_mulai'        => $j[3],
                    'jam_selesai'      => $j[4],
                    'gedung_id'        => $j[5]->gedung_id,
                    'ruangan_id'       => $j[5]->id,
                ]);
            }
        }
    }
}
