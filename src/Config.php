<?php

namespace InfobipBot;

use Dotenv\Dotenv;

class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    /**
     * تحميل ملف .env والتكوينات
     */
    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        self::$config = [
            'infobip' => [
                'base_url' => $_ENV['INFOBIP_BASE_URL'] ?? '',
                'api_key' => $_ENV['INFOBIP_API_KEY'] ?? '',
            ],
            'telegram' => [
                'bot_token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? '',
                'chat_id' => $_ENV['TELEGRAM_CHAT_ID'] ?? '',
            ],
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'name' => $_ENV['DB_NAME'] ?? 'infobip_bot',
            ],
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => $_ENV['APP_DEBUG'] ?? false,
            ],
        ];

        self::$loaded = true;
    }

    /**
     * الحصول على قيمة التكوين
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * الحصول على جميع التكوينات
     */
    public static function all(): array
    {
        self::load();
        return self::$config;
    }
}
