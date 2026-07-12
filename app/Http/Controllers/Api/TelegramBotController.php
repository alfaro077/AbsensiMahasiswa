<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalMataKuliah;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TelegramBotController extends Controller
{
    public function webhook(Request $request, TelegramService $telegram): JsonResponse
    {
        $update = $request->all();
        $message = $update['message'] ?? null;

        if (!$message) {
            return response()->json(['ok' => false]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        if (!$chatId) {
            return response()->json(['ok' => false]);
        }

        if (str_starts_with($text, '/start')) {
            $params = explode(' ', $text, 2);
            $token = $params[1] ?? null;

            if ($token) {
                $user = User::where('telegram_token', $token)->first();
                if ($user) {
                    $user->telegram_chat_id = (string) $chatId;
                    $user->telegram_token = null;
                    $user->save();

                    $telegram->sendMessage($chatId, "✅ Akun Anda berhasil terhubung! Anda akan menerima notifikasi jadwal setiap pagi.");
                } else {
                    $telegram->sendMessage($chatId, "❌ Token tidak valid atau sudah kedaluwarsa. Silakan buka halaman Profile di aplikasi untuk mendapatkan link baru.");
                }
            } else {
                $telegram->sendMessage($chatId, "Halo! Untuk menghubungkan akun, silakan buka menu Profile di aplikasi dan klik 'Hubungkan Telegram'.");
            }
        }

        return response()->json(['ok' => true]);
    }

    public function generateLink(Request $request): JsonResponse
    {
        $user = $request->user();
        $botUsername = config('services.telegram.bot_username');

        if (empty($botUsername)) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram bot username belum dikonfigurasi.',
            ], 500);
        }

        if ($user->telegram_chat_id) {
            return response()->json([
                'success' => true,
                'message' => 'Akun Telegram sudah terhubung.',
                'data' => [
                    'connected' => true,
                    'nama' => $user->nama,
                ],
            ]);
        }

        $token = $user->telegram_token ?? (string) Str::uuid();
        $user->telegram_token = $token;
        $user->save();

        $link = "https://t.me/{$botUsername}?start={$token}";

        return response()->json([
            'success' => true,
            'message' => 'Link Telegram berhasil dibuat.',
            'data' => [
                'connected' => false,
                'link' => $link,
                'token' => $token,
            ],
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'connected' => !empty($user->telegram_chat_id),
                'chat_id' => $user->telegram_chat_id,
                'nama' => $user->nama,
            ],
        ]);
    }

    public function unlink(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->telegram_chat_id = null;
        $user->telegram_token = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Akun Telegram berhasil diputuskan.',
        ]);
    }

    public function botStatus(TelegramService $telegram): JsonResponse
    {
        $valid = $telegram->isValidToken();
        $webhook = $telegram->getWebhookInfo();

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => $valid,
                'bot_username' => config('services.telegram.bot_username'),
                'has_token' => !empty(config('services.telegram.bot_token')),
                'webhook' => $webhook,
            ],
        ]);
    }

    public function connectedUsers(): JsonResponse
    {
        $users = User::whereNotNull('telegram_chat_id')
            ->select('id', 'nama', 'email', 'role', 'telegram_chat_id')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function testSend(Request $request, TelegramService $telegram): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:custom,schedule',
            'message' => 'nullable|string|max:4096',
        ]);

        $user = User::find($validated['user_id']);

        if (empty($user->telegram_chat_id)) {
            return response()->json([
                'success' => false,
                'message' => "User {$user->nama} belum menghubungkan Telegram.",
            ], 422);
        }

        if ($validated['type'] === 'custom') {
            $pesan = $validated['message'] ?? 'Pesan uji coba dari Admin.';
        } else {
            $hariIndonesia = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
            $hari = $hariIndonesia[now()->format('l')] ?? now()->format('l');

            $jadwalHariIni = JadwalMataKuliah::with(['mataKuliah', 'gedung', 'ruangan', 'kelasParalel'])
                ->where('hari', $hari)
                ->get();

            if ($jadwalHariIni->isEmpty()) {
                $pesan = "📋 <b>Notifikasi Jadwal (Simulasi)</b>\n\nTidak ada jadwal untuk hari ini ({$hari}).\n\n_Ini adalah pesan uji coba dari Admin._";
            } else {
                $lines = ["📋 <b>Notifikasi Jadwal Hari Ini ({$hari}) — Simulasi</b>\n"];
                foreach ($jadwalHariIni as $j) {
                    $mk = $j->mataKuliah;
                    if (!$mk) continue;
                    $jam = substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5);
                    $kelas = $j->kelasParalel?->nama_kelas ? " (Kelas {$j->kelasParalel->nama_kelas})" : '';
                    $ruang = $j->ruangan?->nama ? " - {$j->ruangan->nama}" : '';
                    $lines[] = "━━━━━━━━━━━━━━━━━━━\n📚 {$mk->nama}{$kelas}\n🕐 {$jam}\n🏢 {$j->gedung?->nama}{$ruang}";
                }
                $lines[] = "━━━━━━━━━━━━━━━━━━━\n_Ini adalah pesan uji coba dari Admin._";
                $pesan = implode("\n", $lines);
            }
        }

        $result = $telegram->sendMessage($user->telegram_chat_id, $pesan);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => "Pesan berhasil dikirim ke {$user->nama}.",
                'data' => [
                    'user_nama' => $user->nama,
                    'chat_id' => $user->telegram_chat_id,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Gagal mengirim pesan ke {$user->nama}. Pastikan user sudah chat bot.",
        ], 500);
    }
}
