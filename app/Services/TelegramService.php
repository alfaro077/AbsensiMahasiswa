<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    public function sendMessage(string $chatId, string $text, array $extra = []): ?array
    {
        if (empty($this->botToken)) {
            Log::warning('Telegram bot token not configured');
            return null;
        }

        try {
            $response = Http::timeout(10)->post($this->apiUrl . 'sendMessage', array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ], $extra));

            $body = $response->json();

            if (!$response->successful() || !($body['ok'] ?? false)) {
                Log::error('Telegram API error', [
                    'chat_id' => $chatId,
                    'response' => $body,
                ]);
                return null;
            }

            return $body;
        } catch (\Exception $e) {
            Log::error('Telegram sendMessage exception: ' . $e->getMessage());
            return null;
        }
    }

    public function setWebhook(string $url): ?array
    {
        try {
            $response = Http::timeout(10)->post($this->apiUrl . 'setWebhook', [
                'url' => $url,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram setWebhook exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getWebhookInfo(): ?array
    {
        try {
            $response = Http::timeout(10)->post($this->apiUrl . 'getWebhookInfo');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram getWebhookInfo exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getUpdates(?int $offset = null): ?array
    {
        try {
            $params = ['timeout' => 5];
            if ($offset !== null) {
                $params['offset'] = $offset;
            }
            $response = Http::timeout(10)->post($this->apiUrl . 'getUpdates', $params);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram getUpdates exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getMe(): ?array
    {
        if (empty($this->botToken)) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get($this->apiUrl . 'getMe');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Telegram getMe exception: ' . $e->getMessage());
            return null;
        }
    }

    public function isValidToken(): bool
    {
        $result = $this->getMe();
        return $result !== null && ($result['ok'] ?? false) === true;
    }
}
