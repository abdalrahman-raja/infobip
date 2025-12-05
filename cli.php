#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use InfobipBot\Config;
use InfobipBot\Services\TelegramService;
use InfobipBot\Services\InfobipService;

Config::load();

$command = $argv[1] ?? 'help';
$args = array_slice($argv, 2);

$telegramService = new TelegramService();
$infobipService = new InfobipService();

match ($command) {
    'test-telegram' => testTelegram($telegramService),
    'test-infobip' => testInfobip($infobipService),
    'send-sms' => sendSms($infobipService, $args),
    'send-test-message' => sendTestMessage($telegramService),
    'help' => showHelp(),
    default => echo "❌ أمر غير معروف: {$command}\n\nاستخدم: php cli.php help\n",
};

function testTelegram(TelegramService $service): void
{
    echo "🧪 اختبار اتصال Telegram...\n";

    $result = $service->getMe();

    if ($result['success']) {
        $bot = $result['bot'];
        echo "✅ الاتصال ناجح!\n";
        echo "   اسم البوت: {$bot['first_name']}\n";
        echo "   معرف البوت: {$bot['id']}\n";
        echo "   اسم المستخدم: @{$bot['username']}\n";
    } else {
        echo "❌ فشل الاتصال: {$result['error']}\n";
    }
}

function testInfobip(InfobipService $service): void
{
    echo "🧪 اختبار اتصال Infobip...\n";

    // محاولة إرسال رسالة اختبار
    $result = $service->sendSms(
        '+201001234567',
        'اختبار من بوت Infobip',
        'InfoBot'
    );

    if ($result['success']) {
        echo "✅ الاتصال ناجح!\n";
        echo "   معرف الرسالة: {$result['messages'][0]->getMessageId()}\n";
    } else {
        echo "❌ فشل الاتصال: {$result['error']}\n";
    }
}

function sendSms(InfobipService $service, array $args): void
{
    if (count($args) < 2) {
        echo "❌ استخدام غير صحيح\n";
        echo "   php cli.php send-sms <phone> <message>\n";
        echo "   مثال: php cli.php send-sms +201001234567 'مرحبا'\n";
        return;
    }

    $phone = $args[0];
    $message = implode(' ', array_slice($args, 1));

    echo "📱 إرسال رسالة SMS...\n";
    echo "   الرقم: {$phone}\n";
    echo "   الرسالة: {$message}\n\n";

    $result = $service->sendSms($phone, $message);

    if ($result['success']) {
        echo "✅ تم إرسال الرسالة بنجاح!\n";
        echo "   معرف الرسالة: {$result['messages'][0]->getMessageId()}\n";
    } else {
        echo "❌ فشل إرسال الرسالة: {$result['error']}\n";
    }
}

function sendTestMessage(TelegramService $service): void
{
    $chatId = Config::get('telegram.chat_id');

    echo "📧 إرسال رسالة اختبار إلى Telegram...\n";
    echo "   معرف الدردشة: {$chatId}\n\n";

    $result = $service->sendMessage(
        $chatId,
        "✅ <b>رسالة اختبار</b>\n\nتم إرسال هذه الرسالة من سطر الأوامر بنجاح!"
    );

    if ($result['success']) {
        echo "✅ تم إرسال الرسالة بنجاح!\n";
        echo "   معرف الرسالة: {$result['message_id']}\n";
    } else {
        echo "❌ فشل إرسال الرسالة: {$result['error']}\n";
    }
}

function showHelp(): void
{
    echo "\n";
    echo "🤖 بوت Infobip Telegram - أداة سطر الأوامر\n";
    echo "═══════════════════════════════════════════════════════\n\n";
    echo "الأوامر المتاحة:\n\n";
    echo "  <b>test-telegram</b>          اختبار اتصال Telegram\n";
    echo "  <b>test-infobip</b>           اختبار اتصال Infobip\n";
    echo "  <b>send-sms</b> <phone> <msg> إرسال رسالة SMS\n";
    echo "  <b>send-test-message</b>      إرسال رسالة اختبار\n";
    echo "  <b>help</b>                   عرض هذه المساعدة\n\n";
    echo "أمثلة:\n";
    echo "  php cli.php test-telegram\n";
    echo "  php cli.php send-sms +201001234567 'مرحا'\n";
    echo "  php cli.php send-test-message\n\n";
}
