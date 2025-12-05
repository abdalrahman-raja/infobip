<?php

namespace InfobipBot\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InfobipBot\Config;

class TelegramService
{
    private Client $client;
    private string $botToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = Config::get('telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}";
        $this->client = new Client();
    }

    /**
     * إرسال رسالة نصية
     *
     * @param int|string $chatId معرف الدردشة
     * @param string $text نص الرسالة
     * @param array $options خيارات إضافية
     * @return array
     */
    public function sendMessage($chatId, string $text, array $options = []): array
    {
        try {
            $payload = array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ], $options);

            $response = $this->client->post("{$this->apiUrl}/sendMessage", [
                'json' => $payload
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
                'message_id' => $data['result']['message_id'] ?? null,
                'data' => $data['result'] ?? null,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * إرسال رسالة مع أزرار
     *
     * @param int|string $chatId معرف الدردشة
     * @param string $text نص الرسالة
     * @param array $buttons الأزرار
     * @return array
     */
    public function sendMessageWithButtons($chatId, string $text, array $buttons): array
    {
        $keyboard = [
            'inline_keyboard' => $buttons
        ];

        return $this->sendMessage($chatId, $text, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * تعديل رسالة موجودة
     *
     * @param int|string $chatId معرف الدردشة
     * @param int $messageId معرف الرسالة
     * @param string $text النص الجديد
     * @return array
     */
    public function editMessage($chatId, int $messageId, string $text): array
    {
        try {
            $response = $this->client->post("{$this->apiUrl}/editMessageText", [
                'json' => [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
                'data' => $data['result'] ?? null,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * حذف رسالة
     *
     * @param int|string $chatId معرف الدردشة
     * @param int $messageId معرف الرسالة
     * @return array
     */
    public function deleteMessage($chatId, int $messageId): array
    {
        try {
            $response = $this->client->post("{$this->apiUrl}/deleteMessage", [
                'json' => [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * الرد على استعلام callback
     *
     * @param string $callbackQueryId معرف الاستعلام
     * @param string $text النص المراد عرضه
     * @param bool $alert هل يتم عرض تنبيه
     * @return array
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $alert = false): array
    {
        try {
            $response = $this->client->post("{$this->apiUrl}/answerCallbackQuery", [
                'json' => [
                    'callback_query_id' => $callbackQueryId,
                    'text' => $text,
                    'show_alert' => $alert,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * الحصول على معلومات البوت
     *
     * @return array
     */
    public function getMe(): array
    {
        try {
            $response = $this->client->get("{$this->apiUrl}/getMe");
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
                'bot' => $data['result'] ?? null,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * تعيين webhook
     *
     * @param string $url رابط الـ webhook
     * @return array
     */
    public function setWebhook(string $url): array
    {
        try {
            $response = $this->client->post("{$this->apiUrl}/setWebhook", [
                'json' => [
                    'url' => $url,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
                'data' => $data['result'] ?? null,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * حذف webhook
     *
     * @return array
     */
    public function deleteWebhook(): array
    {
        try {
            $response = $this->client->post("{$this->apiUrl}/deleteWebhook");
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => $data['ok'] ?? false,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
