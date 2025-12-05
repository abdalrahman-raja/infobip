#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use InfobipBot\Config;

Config::load();

$host = '127.0.0.1';
$port = 8000;

echo "🚀 بدء خادم Infobip Telegram Bot\n";
echo "═══════════════════════════════════════════════════════\n\n";
echo "📍 الخادم: http://{$host}:{$port}\n";
echo "📝 ملف Webhook: http://{$host}:{$port}/webhook.php\n";
echo "🔧 وضع التصحيح: " . (Config::get('app.debug') ? '✅ مفعل' : '❌ معطل') . "\n\n";

echo "لإيقاف الخادم، اضغط Ctrl+C\n";
echo "═══════════════════════════════════════════════════════\n\n";

// بدء الخادم المدمج
$command = "php -S {$host}:{$port} -t " . __DIR__;
passthru($command);
