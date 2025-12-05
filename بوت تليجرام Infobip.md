# بوت تليجرام Infobip

هذا المشروع هو بوت تليجرام تم تطويره باستخدام PHP، وهو مصمم للتكامل مع واجهة برمجة تطبيقات Infobip لإرسال رسائل SMS و WhatsApp و Email مباشرة من خلال Telegram.

## الميزات

- **إرسال متعدد القنوات**: يدعم إرسال الرسائل عبر SMS و WhatsApp و Email.
- **واجهة تفاعلية**: يستخدم أزرارًا وقوائم تفاعلية لتسهيل الاستخدام.
- **إدارة الأوامر**: نظام مرن لمعالجة الأوامر مثل `/start` و `/help` و `/send_sms`.
- **تكوين مرن**: يستخدم ملف `.env` لإدارة بيانات الاعتماد والتكوينات بسهولة.
- **قابلية للتوسعة**: بنية المشروع منظمة (Services, Commands, Models) لتسهيل إضافة ميزات جديدة.
- **أدوات مساعدة**: يتضمن واجهة سطر أوامر (CLI) للاختبار والإدارة.

## متطلبات التشغيل

- **PHP**: إصدار 8.1 أو أحدث.
- **Composer**: لإدارة الاعتماديات.
- **حساب Infobip**: للحصول على `API Key` و `Base URL`.
- **بوت تليجرام**: للحصول على `Bot Token`.

## كيفية التثبيت

1. **استنساخ المستودع**:
   ```bash
   git clone https://github.com/your-username/infobip-telegram-bot.git
   cd infobip-telegram-bot
   ```

2. **تثبيت الاعتماديات**:
   ```bash
   composer install
   ```

3. **إعداد ملف التكوين**:
   - انسخ ملف `.env.example` إلى `.env`:
     ```bash
     cp .env.example .env
     ```
   - قم بتحرير ملف `.env` وأضف بيانات الاعتماد الخاصة بك:
     ```ini
     # Infobip Configuration
     INFOBIP_BASE_URL=your_base_url
     INFOBIP_API_KEY=your_api_key

     # Telegram Configuration
     TELEGRAM_BOT_TOKEN=your_bot_token
     TELEGRAM_CHAT_ID=your_chat_id
     ```

## كيفية الاستخدام

### 1. تشغيل الخادم المحلي (للتطوير)

يمكنك استخدام الخادم المدمج في PHP لتشغيل البوت محليًا:

```bash
php server.php
```

سيقوم هذا الأمر بتشغيل خادم على `http://127.0.0.1:8000`.

### 2. إعداد Webhook

لكي يتمكن Telegram من إرسال التحديثات إلى البوت الخاص بك، تحتاج إلى تعيين Webhook. ستحتاج إلى أداة مثل `ngrok` لإنشاء نفق آمن إلى خادمك المحلي.

1. **تشغيل ngrok**:
   ```bash
   ngrok http 8000
   ```

2. **الحصول على رابط HTTPS**:
   سيقوم `ngrok` بتوفير رابط `https` (مثال: `https://xxxx-xxxx.ngrok.io`).

3. **تعيين Webhook**:
   استخدم الرابط الذي حصلت عليه لتعيين Webhook من خلال Telegram API:
   ```bash
   curl -F "url=https://xxxx-xxxx.ngrok.io/webhook.php" https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook
   ```
   استبدل `<YOUR_BOT_TOKEN>` برمز البوت الخاص بك.

### 3. التفاعل مع البوت

- افتح تطبيق Telegram وابحث عن البوت الخاص بك.
- أرسل الأمر `/start` لبدء التفاعل.
- استخدم الأزرار التفاعلية لإرسال الرسائل عبر القنوات المختلفة.

## واجهة سطر الأوامر (CLI)

يتضمن المشروع أداة CLI للمساعدة في الاختبار والإدارة:

- **اختبار اتصال Telegram**:
  ```bash
  php cli.php test-telegram
  ```

- **إرسال رسالة اختبار إلى Telegram**:
  ```bash
  php cli.php send-test-message
  ```

- **إرسال رسالة SMS**:
  ```bash
  php cli.php send-sms +1234567890 "Hello from CLI"
  ```

- **عرض المساعدة**:
  ```bash
  php cli.php help
  ```

## بنية المشروع

```
infobip-telegram-bot/
├── src/                    # الكود المصدري
│   ├── Services/           # خدمات (Infobip, Telegram)
│   ├── Models/             # نماذج البيانات (إذا لزم الأمر)
│   ├── Commands/           # معالجات الأوامر
│   └── CommandHandler.php  # معالج الأوامر الرئيسي
│   └── Config.php          # فئة التكوين
├── vendor/                 # اعتماديات Composer
├── logs/                   # ملفات السجلات
├── .env                    # ملف التكوين
├── .env.example            # مثال على ملف التكوين
├── composer.json           # ملف Composer
├── webhook.php             # نقطة دخول Webhook
├── cli.php                 # واجهة سطر الأوامر
├── server.php              # مشغل الخادم المحلي
└── README.md               # هذا الملف
```

## الترخيص

هذا المشروع مرخص بموجب [ترخيص MIT](LICENSE).
