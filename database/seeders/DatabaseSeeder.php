<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Gedung;
use App\Models\JadwalMataKuliah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Jurusan::truncate();
        Dosen::truncate();
        Mahasiswa::truncate();
        MataKuliah::truncate();
        Gedung::truncate();
        JadwalMataKuliah::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed Gedung
        Gedung::create(['kode' => 'GD-A', 'nama' => 'Gedung A', 'lokasi' => 'Kampus Timur']);
        Gedung::create(['kode' => 'GD-B', 'nama' => 'Gedung B', 'lokasi' => 'Kampus Barat']);
        Gedung::create(['kode' => 'GD-C', 'nama' => 'Gedung C', 'lokasi' => 'Kampus Selatan']);

        // 1. Seed Admin
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Seed Jurusan
        $j1 = Jurusan::create(['kode' => 'TI', 'nama' => 'Teknik Informatika']);
        $j2 = Jurusan::create(['kode' => 'SI', 'nama' => 'Sistem Informasi']);
        $j3 = Jurusan::create(['kode' => 'TE', 'nama' => 'Teknik Elektro']);
        $j4 = Jurusan::create(['kode' => 'MI', 'nama' => 'Manajemen Informatika']);

        $jurusans = [$j1, $j2, $j3, $j4];

        // 3. Seed Dosen (1 per Jurusan)
        $dosens = [];
        $dosenNames = [
            'Budi Susanto, M.Kom' => $j1,
            'Siti Aminah, Ph.D.' => $j2,
            'Dr. Ir. Ahmad Fauzi' => $j3,
            'Lina Marlina, M.T.' => $j4,
        ];

        $i = 1;
        foreach ($dosenNames as $name => $jurusan) {
            $user = User::create([
                'nama' => $name,
                'email' => "dosen$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ]);
            $dosens[] = Dosen::create([
                'user_id' => $user->id,
                'jurusan_id' => $jurusan->id,
                'nip' => '19800101201001100' . $i,
                'jabatan' => $i % 2 == 0 ? 'Lektor' : 'Asisten Ahli',
            ]);
            $i++;
        }

        // 4. Seed Mahasiswa (3 per Jurusan)
        $mhsData = [
            $j1->id => [['Andi Wijaya', '20230001'], ['Bambang Heru', '20230002'], ['Cici Paramida', '20230003']],
            $j2->id => [['Dedi Corbuzier', '20230004'], ['Eka Gustiwana', '20230005'], ['Feni Rose', '20230006']],
            $j3->id => [['Gading Marten', '20230007'], ['Hesti Purwadinata', '20230008'], ['Indra Bekti', '20230009']],
            $j4->id => [['Joko Anwar', '20230010'], ['Kiki Saputri', '20230011'], ['Luna Maya', '20230012']],
        ];

        foreach ($mhsData as $jurusanId => $students) {
            foreach ($students as $data) {
                $user = User::create([
                    'nama' => $data[0],
                    'email' => strtolower(str_replace(' ', '.', $data[0])) . "@mhs.example.com",
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                ]);
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'jurusan_id' => $jurusanId,
                    'nim' => $data[1],
                    'angkatan' => 2023,
                ]);
            }
        }

        // 5. Seed Mata Kuliah (10 per Jurusan)
        $mkList = [
            'TI' => [
                'Algoritma I', 'Matematika Diskrit', 'Sistem Digital', 'Struktur Data', 'Basis Data',
                'Pemrograman Web', 'Rekayasa Perangkat Lunak', 'Jaringan Komputer', 'Sistem Operasi', 'Kecerdasan Buatan'
            ],
            'SI' => [
                'Pengantar SI', 'Algoritma Logika', 'Matematika Bisnis', 'Sistem Basis Data', 'Analisis Proses Bisnis',
                'Desain UI/UX', 'E-Business', 'Analisis Perancangan SI', 'Manajemen Proyek SI', 'Audit SI'
            ],
            'TE' => [
                'Fisika Dasar', 'Kalkulus I', 'Rangkaian Elektrik I', 'Rangkaian Elektrik II', 'Dasar Elektronika',
                'Sinyal dan Sistem', 'Elektromagnetika', 'Sistem Kontrol', 'Mikrokontroler', 'Pengolahan Sinyal Digital'
            ],
            'MI' => [
                'Pengantar Manajemen', 'Pengantar TI', 'Praktikum Office', 'Basis Data Terapan', 'Pemrograman Visual',
                'Jaringan Komputer Dasar', 'Web Development', 'SI Manajemen', 'E-Commerce', 'Etika Profesi'
            ]
        ];

        foreach ($mkList as $kode => $names) {
            $jurusan = Jurusan::where('kode', $kode)->first();
            $dosen = Dosen::where('jurusan_id', $jurusan->id)->first();
            
            foreach ($names as $idx => $name) {
                MataKuliah::create([
                    'kode' => $kode . (101 + $idx),
                    'nama' => $name,
                    'sks' => rand(2, 4),
                    'semester' => floor($idx / 2) + 1,
                    'jurusan_id' => $jurusan->id,
                    'dosen_id' => $dosen->id,
                ]);
            }
        }
    }
}
