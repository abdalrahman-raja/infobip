# تحليل مستودع Infobip API PHP Client

## معلومات المشروع
- **الاسم**: infobip/infobip-api-php-client
- **الإصدار الحالي**: 6.2.1
- **الترخيص**: MIT
- **متطلبات PHP**: >= 8.3

## الميزات الرئيسية المدعومة
1. **SMS** - إرسال الرسائل النصية
2. **2FA** - المصادقة الثنائية
3. **Messages API** - واجهة برمجية للرسائل
4. **MMS** - الرسائل الوسائط المتعددة
5. **Voice** - الاتصالات الصوتية
6. **WebRTC** - اتصالات الويب
7. **Email** - البريد الإلكتروني
8. **WhatsApp** - تطبيق واتس آب
9. **Viber** - تطبيق فايبر
10. **Moments** - خدمة اللحظات

## طريقة الاستخدام الأساسية

### التثبيت
```json
"require": {
    "infobip/infobip-api-php-client": "6.2.1"
}
```

### التكوين
```php
use Infobip\Configuration;

$configuration = new Configuration(
    host: 'your-base-url',
    apiKey: 'your-api-key'
);
```

### إرسال SMS
```php
use Infobip\ApiException;
use Infobip\Model\SmsRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsMessage;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsTextContent;

$sendSmsApi = new SmsApi(config: $configuration);

$message = new SmsMessage(
    destinations: [
        new SmsDestination(to: '41793026727')
    ],
    content: new SmsTextContent(
        text: 'Your message here'
    ),
    sender: 'InfoSMS'
);

$request = new SmsRequest(messages: [$message]);

try {
    $smsResponse = $sendSmsApi->sendSmsMessages($request);
} catch (ApiException $apiException) {
    // Handle exception
}
```

## الخطوات التالية
- بناء بوت تليجرام يستخدم هذا العميل
- تطبيق ميزات الرسائل والتفاعل مع المستخدمين
