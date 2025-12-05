<?php

require_once __DIR__ . '/vendor/autoload.php';

use InfobipBot\Config;
use InfobipBot\CommandHandler;

// تحميل التكوينات
Config::load();

// قراءة البيانات الواردة من Telegram
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// تسجيل الطلبات للتصحيح
if (Config::get('app.debug')) {
    file_put_contents(
        __DIR__ . '/logs/webhook.log',
        date('Y-m-d H:i:s') . ' - ' . json_encode($update) . "\n",
        FILE_APPEND
    );
}

// التحقق من أن لدينا بيانات صحيحة
if (!$update || (!isset($update['message']) && !isset($update['callback_query']))) {
    http_response_code(400);
    exit('Invalid request');
}

try {
    // معالجة التحديث
    $handler = new CommandHandler($update);
    $handler->handle();

    http_response_code(200);
    echo json_encode(['status' => 'ok']);
} catch (\Exception $e) {
    // تسجيل الخطأ
    file_put_contents(
        __DIR__ . '/logs/error.log',
        date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . "\n",
        FILE_APPEND
    );

    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
