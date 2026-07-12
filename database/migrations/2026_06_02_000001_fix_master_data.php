<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // 1. Buat dosen TE (Teknik Elektro) jika belum ada
            $teDosen = DB::table('dosen')
                ->join('users', 'dosen.user_id', '=', 'users.id')
                ->where('dosen.jurusan_id', 3)
                ->first();

            if (!$teDosen) {
                $teUserId = DB::table('users')->insertGetId([
                    'nama'     => 'Ahmad Rizky, S.T., M.Kom.',
                    'email'    => 'ahmad@dosen.example.com',
                    'password' => Hash::make('password'),
                    'role'     => 'dosen',
                ]);
                DB::table('dosen')->insert([
                    'user_id'    => $teUserId,
                    'jurusan_id' => 3,
                    'nip'        => '199001012019011002',
                    'jabatan'    => 'Lektor',
                ]);
            }

            // 2. Buat dosen MI (Manajemen Informatika) jika belum ada
            $miDosen = DB::table('dosen')
                ->join('users', 'dosen.user_id', '=', 'users.id')
                ->where('dosen.jurusan_id', 4)
                ->first();

            if (!$miDosen) {
                $miUserId = DB::table('users')->insertGetId([
                    'nama'     => 'Dewi Lestari, S.Kom., M.T.',
                    'email'    => 'dewi@dosen.example.com',
                    'password' => Hash::make('password'),
                    'role'     => 'dosen',
                ]);
                DB::table('dosen')->insert([
                    'user_id'    => $miUserId,
                    'jurusan_id' => 4,
                    'nip'        => '199202022020012003',
                    'jabatan'    => 'Asisten Ahli',
                ]);
            }

            // 3. Perbaiki dosen_id pada mata_kuliah yang mismatch
            $teDosenId = DB::table('dosen')->where('jurusan_id', 3)->value('id');
            $miDosenId = DB::table('dosen')->where('jurusan_id', 4)->value('id');

            if ($teDosenId) {
                DB::table('mata_kuliah')
                    ->where('jurusan_id', 3)
                    ->update(['dosen_id' => $teDosenId]);
            }

            if ($miDosenId) {
                DB::table('mata_kuliah')
                    ->where('jurusan_id', 4)
                    ->update(['dosen_id' => $miDosenId]);
            }

            // 4. Hapus mata_kuliah yang melebihi 5 per jurusan
            foreach ([1, 2, 3, 4] as $jurusanId) {
                $keepIds = DB::table('mata_kuliah')
                    ->where('jurusan_id', $jurusanId)
                    ->orderBy('id')
                    ->limit(5)
                    ->pluck('id');

                if ($keepIds->isNotEmpty()) {
                    DB::table('mata_kuliah')
                        ->where('jurusan_id', $jurusanId)
                        ->whereNotIn('id', $keepIds)
                        ->delete();
                }
            }
        });
    }

    public function down(): void
    {
        // Tidak ada rollback otomatis — restore dari SQL dump jika diperlukan
    }
};
