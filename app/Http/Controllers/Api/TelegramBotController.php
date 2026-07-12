<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
