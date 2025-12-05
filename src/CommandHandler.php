<?php

namespace InfobipBot;

use InfobipBot\Services\TelegramService;
use InfobipBot\Services\InfobipService;

class CommandHandler
{
    private TelegramService $telegramService;
    private InfobipService $infobipService;
    private array $update;
    private int $chatId;
    private int $userId;
    private string $messageText;
    private ?string $callbackData = null;

    public function __construct(array $update)
    {
        $this->update = $update;
        $this->telegramService = new TelegramService();
        $this->infobipService = new InfobipService();

        // استخراج البيانات من التحديث
        if (isset($update['message'])) {
            $this->chatId = $update['message']['chat']['id'];
            $this->userId = $update['message']['from']['id'];
            $this->messageText = $update['message']['text'] ?? '';
        } elseif (isset($update['callback_query'])) {
            $this->chatId = $update['callback_query']['message']['chat']['id'];
            $this->userId = $update['callback_query']['from']['id'];
            $this->callbackData = $update['callback_query']['data'] ?? '';
            $this->messageText = '';
        }
    }

    /**
     * معالجة التحديث
     */
    public function handle(): void
    {
        if (isset($this->update['message'])) {
            $this->handleMessage();
        } elseif (isset($this->update['callback_query'])) {
            $this->handleCallbackQuery();
        }
    }

    /**
     * معالجة الرسالة النصية
     */
    private function handleMessage(): void
    {
        $text = trim($this->messageText);

        if (strpos($text, '/') === 0) {
            $this->handleCommand($text);
        } else {
            $this->showMainMenu();
        }
    }

    /**
     * معالجة الأوامر
     */
    private function handleCommand(string $command): void
    {
        $parts = explode(' ', $command, 2);
        $cmd = strtolower($parts[0]);
        $args = $parts[1] ?? '';

        match ($cmd) {
            '/start' => $this->commandStart(),
            '/help' => $this->commandHelp(),
            '/send_sms' => $this->commandSendSms($args),
            '/send_whatsapp' => $this->commandSendWhatsApp($args),
            '/send_email' => $this->commandSendEmail($args),
            '/status' => $this->commandStatus(),
            '/about' => $this->commandAbout(),
            default => $this->telegramService->sendMessage(
                $this->chatId,
                "❌ أمر غير معروف: <b>{$cmd}</b>\n\nاستخدم /help للمزيد من المعلومات"
            ),
        };
    }

    /**
     * معالجة استعلامات Callback
     */
    private function handleCallbackQuery(): void
    {
        $data = $this->callbackData;
        $parts = explode(':', $data);
        $action = $parts[0] ?? '';

        match ($action) {
            'send_sms' => $this->showSmsForm(),
            'send_whatsapp' => $this->showWhatsAppForm(),
            'send_email' => $this->showEmailForm(),
            'main_menu' => $this->showMainMenu(),
            default => $this->telegramService->answerCallbackQuery(
                $this->update['callback_query']['id'],
                'إجراء غير معروف'
            ),
        };

        // الرد على الاستعلام
        $this->telegramService->answerCallbackQuery(
            $this->update['callback_query']['id']
        );
    }

    /**
     * أمر البداية
     */
    private function commandStart(): void
    {
        $message = "🤖 <b>مرحباً بك في بوت Infobip!</b>\n\n";
        $message .= "هذا البوت يساعدك على إرسال الرسائل عبر Infobip:\n";
        $message .= "• 📱 رسائل SMS\n";
        $message .= "• 💬 رسائل WhatsApp\n";
        $message .= "• 📧 رسائل Email\n\n";
        $message .= "استخدم القائمة أدناه للبدء!";

        $this->showMainMenu($message);
    }

    /**
     * أمر المساعدة
     */
    private function commandHelp(): void
    {
        $message = "📖 <b>قائمة الأوامر المتاحة:</b>\n\n";
        $message .= "<b>/start</b> - البدء والقائمة الرئيسية\n";
        $message .= "<b>/help</b> - عرض هذه المساعدة\n";
        $message .= "<b>/send_sms</b> - إرسال رسالة SMS\n";
        $message .= "<b>/send_whatsapp</b> - إرسال رسالة WhatsApp\n";
        $message .= "<b>/send_email</b> - إرسال رسالة Email\n";
        $message .= "<b>/status</b> - حالة البوت\n";
        $message .= "<b>/about</b> - معلومات عن البوت\n";

        $this->telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * أمر إرسال SMS
     */
    private function commandSendSms(string $args): void
    {
        if (empty($args)) {
            $this->showSmsForm();
            return;
        }

        // معالجة البيانات المرسلة
        $this->telegramService->sendMessage(
            $this->chatId,
            "📱 <b>إرسال رسالة SMS</b>\n\nالرجاء إدخال البيانات:\n\n<code>/send_sms +1234567890 نص الرسالة</code>"
        );
    }

    /**
     * أمر إرسال WhatsApp
     */
    private function commandSendWhatsApp(string $args): void
    {
        $this->showWhatsAppForm();
    }

    /**
     * أمر إرسال Email
     */
    private function commandSendEmail(string $args): void
    {
        $this->showEmailForm();
    }

    /**
     * أمر الحالة
     */
    private function commandStatus(): void
    {
        $botInfo = $this->telegramService->getMe();

        if ($botInfo['success']) {
            $bot = $botInfo['bot'];
            $message = "✅ <b>حالة البوت</b>\n\n";
            $message .= "<b>الحالة:</b> 🟢 نشط\n";
            $message .= "<b>اسم البوت:</b> {$bot['first_name']}\n";
            $message .= "<b>معرف البوت:</b> <code>{$bot['id']}</code>\n";
            $message .= "<b>اسم المستخدم:</b> @{$bot['username']}\n";
        } else {
            $message = "❌ <b>خطأ في الاتصال</b>\n\nلم يتمكن البوت من الاتصال بـ Telegram API";
        }

        $this->telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * أمر المعلومات
     */
    private function commandAbout(): void
    {
        $message = "ℹ️ <b>معلومات عن البوت</b>\n\n";
        $message .= "<b>الإصدار:</b> 1.0.0\n";
        $message .= "<b>المطور:</b> Infobip Bot Team\n";
        $message .= "<b>الترخيص:</b> MIT\n";
        $message .= "<b>الموقع:</b> <a href='https://github.com'>GitHub</a>\n\n";
        $message .= "هذا البوت يستخدم Infobip API لإرسال الرسائل.";

        $this->telegramService->sendMessage($this->chatId, $message);
    }

    /**
     * عرض القائمة الرئيسية
     */
    private function showMainMenu(string $customMessage = null): void
    {
        $message = $customMessage ?? "📋 <b>القائمة الرئيسية</b>\n\nاختر الخدمة المطلوبة:";

        $buttons = [
            [
                ['text' => '📱 إرسال SMS', 'callback_data' => 'send_sms'],
                ['text' => '💬 إرسال WhatsApp', 'callback_data' => 'send_whatsapp'],
            ],
            [
                ['text' => '📧 إرسال Email', 'callback_data' => 'send_email'],
            ],
        ];

        $this->telegramService->sendMessageWithButtons($this->chatId, $message, $buttons);
    }

    /**
     * عرض نموذج SMS
     */
    private function showSmsForm(): void
    {
        $message = "📱 <b>إرسال رسالة SMS</b>\n\n";
        $message .= "الرجاء إدخال البيانات التالية:\n";
        $message .= "1️⃣ رقم الهاتف (مثال: +201001234567)\n";
        $message .= "2️⃣ نص الرسالة\n\n";
        $message .= "أرسل الرسالة بالصيغة:\n";
        $message .= "<code>رقم_الهاتف | نص_الرسالة</code>";

        $buttons = [
            [
                ['text' => '⬅️ العودة', 'callback_data' => 'main_menu'],
            ],
        ];

        $this->telegramService->sendMessageWithButtons($this->chatId, $message, $buttons);
    }

    /**
     * عرض نموذج WhatsApp
     */
    private function showWhatsAppForm(): void
    {
        $message = "💬 <b>إرسال رسالة WhatsApp</b>\n\n";
        $message .= "الرجاء إدخال البيانات التالية:\n";
        $message .= "1️⃣ رقم الهاتف (مثال: +201001234567)\n";
        $message .= "2️⃣ نص الرسالة\n\n";
        $message .= "أرسل الرسالة بالصيغة:\n";
        $message .= "<code>رقم_الهاتف | نص_الرسالة</code>";

        $buttons = [
            [
                ['text' => '⬅️ العودة', 'callback_data' => 'main_menu'],
            ],
        ];

        $this->telegramService->sendMessageWithButtons($this->chatId, $message, $buttons);
    }

    /**
     * عرض نموذج Email
     */
    private function showEmailForm(): void
    {
        $message = "📧 <b>إرسال رسالة Email</b>\n\n";
        $message .= "الرجاء إدخال البيانات التالية:\n";
        $message .= "1️⃣ عنوان البريد الإلكتروني\n";
        $message .= "2️⃣ الموضوع\n";
        $message .= "3️⃣ نص الرسالة\n\n";
        $message .= "أرسل الرسالة بالصيغة:\n";
        $message .= "<code>البريد | الموضوع | النص</code>";

        $buttons = [
            [
                ['text' => '⬅️ العودة', 'callback_data' => 'main_menu'],
            ],
        ];

        $this->telegramService->sendMessageWithButtons($this->chatId, $message, $buttons);
    }
}
