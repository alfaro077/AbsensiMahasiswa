<?php

use App\Console\Commands\SendScheduleNotification;
use App\Services\TelegramService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('schedule:send-notification {--dry-run : Tampilkan daftar tanpa kirim} {--force : Kirim semua notifikasi ke admin (testing)}', function ($dryRun = false) {
    $this->call(SendScheduleNotification::class, [
        '--dry-run' => $this->option('dry-run'),
        '--force' => $this->option('force'),
    ]);
})->purpose('Kirim notifikasi Telegram jadwal hari ini ke dosen & mahasiswa')->dailyAt('06:00');

Artisan::command('schedule:test-notification', function () {
    $telegram = app(TelegramService::class);
    $botToken = config('services.telegram.bot_token');

    if (empty($botToken)) {
        $this->error('TELEGRAM_BOT_TOKEN belum diisi di file .env!');
        $this->line('Buat bot di @BotFather lalu isi token di .env');
        return 1;
    }

    $this->info("Token bot: " . substr($botToken, 0, 10) . '...');
    if ($telegram->isValidToken()) {
        $this->info('Bot token valid!');
    } else {
        $this->error('Token tidak valid! Token salah atau bot tidak ditemukan.');
        $this->line('Coba periksa token di @BotFather (/mybots -> API Token)');
        $this->line('Pastikan token dimulai dengan angka, titik dua, dan string acak.');
        return 1;
    }

    $adminId = env('TELEGRAM_ADMIN_CHAT_ID');
    if ($adminId) {
        $sent = $telegram->sendMessage($adminId, "✅ Bot AbsensiMahasiswa aktif! Notifikasi jadwal siap.");
        if ($sent) {
            $this->info("Pesan uji coba terkirim ke chat_id: {$adminId}");
        } else {
            $this->warn("Gagal kirim pesan ke TELEGRAM_ADMIN_CHAT_ID ({$adminId}). Mungkin belum chat bot?");
        }
    }

    $this->line("");
    Artisan::call('schedule:send-notification', [
        '--dry-run' => true,
    ]);
    $this->line(Artisan::output());

    $this->newLine();
    $this->info('=== PETUNJUK PENGUJIAN ===');
    $this->line('0. Chat bot Anda di Telegram (cari @namabot, kirim /start)');
    $this->line('1. Isi TELEGRAM_BOT_TOKEN dan TELEGRAM_BOT_USERNAME di .env');
    $this->line('2. Jalankan: php artisan telegram:poll (untuk development lokal)');
    $this->line('3. Buka halaman Profile aplikasi, klik "Hubungkan Telegram"');
    $this->line('4. Klik link yang muncul, tekan Start di Telegram');
    $this->line('5. Jalankan: php artisan schedule:send-notification');
    $this->line('6. Cek Telegram Anda untuk notifikasi jadwal!');
})->purpose('Test notifikasi Telegram & tampilkan petunjuk');

Artisan::command('telegram:set-webhook {url?}', function ($url = null) {
    $telegram = app(TelegramService::class);
    $url = $url ?: config('app.url') . '/api/telegram/webhook';
    $result = $telegram->setWebhook($url);
    if ($result && ($result['ok'] ?? false)) {
        $this->info('Webhook berhasil diatur ke: ' . $url);
    } else {
        $this->error('Gagal mengatur webhook: ' . ($result['description'] ?? 'unknown error'));
    }
})->purpose('Set Telegram bot webhook URL');

Artisan::command('telegram:poll', function () {
    $telegram = app(TelegramService::class);
    $offset = 0;
    $telegram->sendMessage(env('TELEGRAM_ADMIN_CHAT_ID'), 'Bot polling started.');
    $this->info('Polling started...');

    while (true) {
        $updates = $telegram->getUpdates($offset);
        if ($updates && ($updates['ok'] ?? false)) {
            foreach ($updates['result'] as $update) {
                $updateId = $update['update_id'];
                $message = $update['message'] ?? null;
                if ($message) {
                    $chatId = $message['chat']['id'] ?? null;
                    $text = $message['text'] ?? '';
                    $this->line("  [{$updateId}] Chat: {$chatId}, Text: {$text}");

                    if (str_starts_with($text, '/start')) {
                        $params = explode(' ', $text, 2);
                        $token = $params[1] ?? null;
                        if ($token) {
                            $user = \App\Models\User::where('telegram_token', $token)->first();
                            if ($user) {
                                $user->telegram_chat_id = (string) $chatId;
                                $user->telegram_token = null;
                                $user->save();
                                $telegram->sendMessage($chatId, "✅ Akun Anda berhasil terhubung!");
                                $this->info("  User {$user->nama} (ID: {$user->id}) berhasil dihubungkan!");
                            } else {
                                $telegram->sendMessage($chatId, "❌ Token tidak valid.");
                            }
                        } else {
                            $telegram->sendMessage($chatId, "Halo! Untuk menghubungkan, buka Profile di aplikasi.");
                        }
                    }
                }
                $offset = max($offset, $updateId + 1);
            }
        }
        sleep(2);
    }
})->purpose('Poll Telegram updates (alternative to webhook for local dev)');
