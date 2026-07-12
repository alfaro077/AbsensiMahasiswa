<?php

namespace App\Console\Commands;

use App\Models\JadwalMataKuliah;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendScheduleNotification extends Command
{
    protected $signature = 'schedule:send-notification {--dry-run : Tampilkan daftar penerima tanpa kirim pesan} {--force : Kirim semua notifikasi ke admin (untuk testing)}';
    protected $description = 'Kirim notifikasi Telegram ke dosen & mahasiswa yg punya jadwal hari ini';

    protected array $hariIndonesia = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    public function handle(TelegramService $telegram): int
    {
        $hariInggris = now()->format('l');
        $hariIndonesia = $this->hariIndonesia[$hariInggris] ?? $hariInggris;

        $force = $this->option('force');
        $adminChatId = $force ? env('TELEGRAM_ADMIN_CHAT_ID') : null;
        if ($force) {
            if (empty($adminChatId)) {
                $this->error('TELEGRAM_ADMIN_CHAT_ID belum diisi di .env!');
                return 1;
            }
            $this->warn('MODE FORCE: Semua notifikasi dikirim ke admin chat_id');
        }

        $this->info("Hari ini: {$hariIndonesia}");

        $jadwals = JadwalMataKuliah::with([
            'mataKuliah.dosen.user',
            'kelasParalel.mahasiswa.user',
            'gedung',
            'ruangan',
        ])->where('hari', $hariIndonesia)->get();

        if ($jadwals->isEmpty()) {
            $this->warn('Tidak ada jadwal untuk hari ini.');
            return 0;
        }

        $this->line("Ditemukan {$jadwals->count()} jadwal.");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($jadwals as $jadwal) {
            $matkul = $jadwal->mataKuliah;
            if (!$matkul) continue;

            $dosenUser = $matkul->dosen?->user;

            $this->line("  [{$matkul->nama}]");

            // Kirim ke dosen
            $dosenChatId = $force ? $adminChatId : $dosenUser?->telegram_chat_id;
            if ($dosenChatId) {
                $pesanDosen = $this->buatPesanDosen($jadwal);
                $this->kirim($telegram, $dosenChatId, $pesanDosen, $sentCount, $errorCount);
                if ($force) $this->line("    [FORCE] Notifikasi dosen dikirim ke admin");
            } elseif ($dosenUser) {
                $this->line("    Dosen: {$dosenUser->nama} (belum hubungkan Telegram)");
            }

            // Kirim ke mahasiswa per kelas paralel
            $mahasiswaUsers = $jadwal->kelasParalel?->mahasiswa->pluck('user')->filter() ?? collect();
            foreach ($mahasiswaUsers as $mhsUser) {
                $mhsChatId = $force ? $adminChatId : $mhsUser->telegram_chat_id;
                if ($mhsChatId) {
                    $pesanMhs = $this->buatPesanMahasiswa($jadwal);
                    $this->kirim($telegram, $mhsChatId, $pesanMhs, $sentCount, $errorCount);
                    if ($force) $this->line("    [FORCE] Notifikasi mahasiswa {$mhsUser->nama} dikirim ke admin");
                } elseif ($this->option('dry-run') && !$force) {
                    $this->line("    Mahasiswa: {$mhsUser->nama} (belum hubungkan Telegram)");
                }
            }
        }

        $this->newLine();
        $this->info("Selesai! Terkirim: {$sentCount}, Gagal: {$errorCount}");

        return 0;
    }

    protected function buatPesanDosen($jadwal): string
    {
        $matkul = $jadwal->mataKuliah;
        $gedung = $jadwal->gedung?->nama ?? 'Gedung tersedia';

        $waktuMulai = \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i');
        $waktuSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');

        $kelasParalel = $jadwal->kelasParalel;
        $jmlMahasiswa = $kelasParalel?->mahasiswa->count() ?? 0;
        $lantai = $jadwal->ruangan?->lantai ?? '-';
        $ruanganNama = $jadwal->ruangan?->nama ?? '-';

        $kelasLabel = $jadwal->kelasParalel?->nama_kelas ? " - Kelas {$jadwal->kelasParalel->nama_kelas}" : '';

        return "Selamat pagi, Bapak/Ibu Dosen! ☀️\n\n"
            . "📋 <b>Jadwal Mengajar Hari Ini</b>\n"
            . "━━━━━━━━━━━━━━━━━━━\n"
            . "📚 <b>{$matkul->nama}</b> ({$matkul->kode}){$kelasLabel}\n"
            . "🕐 {$waktuMulai} - {$waktuSelesai} WIB\n"
            . "🏢 {$gedung} - Lt.{$lantai} - {$ruanganNama}\n"
            . "👥 {$jmlMahasiswa} mahasiswa terdaftar\n"
            . "━━━━━━━━━━━━━━━━━━━\n"
            . "Selamat mengajar! Semoga lancar! ✅";
    }

    protected function buatPesanMahasiswa($jadwal): string
    {
        $matkul = $jadwal->mataKuliah;
        $gedung = $jadwal->gedung?->nama ?? 'Gedung tersedia';

        $waktuMulai = \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i');
        $waktuSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');

        $dosenNama = $matkul->dosen?->user?->nama ?? 'Dosen tersedia';
        $lantai = $jadwal->ruangan?->lantai ?? '-';
        $ruanganNama = $jadwal->ruangan?->nama ?? '-';

        return "Selamat pagi! ☀️\n\n"
            . "📋 <b>Jadwal Kuliah Hari Ini</b>\n"
            . "━━━━━━━━━━━━━━━━━━━\n"
            . "📚 <b>{$matkul->nama}</b> ({$matkul->kode})" . ($jadwal->kelasParalel?->nama_kelas ? " - Kelas {$jadwal->kelasParalel->nama_kelas}" : '') . "\n"
            . "🕐 {$waktuMulai} - {$waktuSelesai} WIB\n"
            . "🏢 {$gedung} - Lt.{$lantai} - {$ruanganNama}\n"
            . "👨‍🏫 {$dosenNama}\n"
            . "━━━━━━━━━━━━━━━━━━━\n"
            . "Jangan lupa hadir tepat waktu! Semangat! 💪";
    }

    protected function kirim(TelegramService $telegram, string $chatId, string $pesan, int &$sentCount, int &$errorCount): void
    {
        if ($this->option('dry-run')) {
            $this->line("  [DRY-RUN] Akan kirim ke chat_id: {$chatId}");
            $sentCount++;
            return;
        }

        $result = $telegram->sendMessage($chatId, $pesan);
        if ($result) {
            $sentCount++;
        } else {
            $errorCount++;
        }
    }
}
