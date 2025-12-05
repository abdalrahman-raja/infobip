<?php

namespace InfobipBot\Services;

use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Api\EmailApi;
use Infobip\Api\WhatsAppApi;
use Infobip\Model\SmsRequest;
use Infobip\Model\SmsMessage;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextContent;
use Infobip\ApiException;
use InfobipBot\Config;

class InfobipService
{
    private Configuration $configuration;
    private SmsApi $smsApi;
    private EmailApi $emailApi;
    private WhatsAppApi $whatsAppApi;

    public function __construct()
    {
        $this->configuration = new Configuration(
            host: Config::get('infobip.base_url'),
            apiKey: Config::get('infobip.api_key')
        );

        $this->smsApi = new SmsApi(config: $this->configuration);
        $this->emailApi = new EmailApi(config: $this->configuration);
        $this->whatsAppApi = new WhatsAppApi(config: $this->configuration);
    }

    /**
     * إرسال رسالة SMS
     *
     * @param string $phoneNumber رقم الهاتف
     * @param string $message نص الرسالة
     * @param string $sender اسم المرسل
     * @return array
     */
    public function sendSms(string $phoneNumber, string $message, string $sender = 'InfoBot'): array
    {
        try {
            $smsMessage = new SmsMessage(
                destinations: [
                    new SmsDestination(to: $phoneNumber)
                ],
                content: new SmsTextContent(text: $message),
                sender: $sender
            );

            $request = new SmsRequest(messages: [$smsMessage]);
            $response = $this->smsApi->sendSmsMessages($request);

            return [
                'success' => true,
                'bulk_id' => $response->getBulkId(),
                'messages' => $response->getMessages(),
                'message' => 'تم إرسال الرسالة بنجاح'
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'message' => 'فشل إرسال الرسالة'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'حدث خطأ غير متوقع'
            ];
        }
    }

    /**
     * إرسال رسائل SMS متعددة
     *
     * @param array $recipients مصفوفة المستقبلين
     * @param string $message نص الرسالة
     * @param string $sender اسم المرسل
     * @return array
     */
    public function sendBulkSms(array $recipients, string $message, string $sender = 'InfoBot'): array
    {
        try {
            $destinations = array_map(
                fn($phone) => new SmsDestination(to: $phone),
                $recipients
            );

            $smsMessage = new SmsMessage(
                destinations: $destinations,
                content: new SmsTextContent(text: $message),
                sender: $sender
            );

            $request = new SmsRequest(messages: [$smsMessage]);
            $response = $this->smsApi->sendSmsMessages($request);

            return [
                'success' => true,
                'bulk_id' => $response->getBulkId(),
                'count' => count($recipients),
                'message' => 'تم إرسال الرسائل بنجاح'
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'message' => 'فشل إرسال الرسائل'
            ];
        }
    }

    /**
     * الحصول على تقرير التسليم
     *
     * @param string|null $bulkId معرف الدفعة
     * @param string|null $messageId معرف الرسالة
     * @return array
     */
    public function getDeliveryReports(?string $bulkId = null, ?string $messageId = null): array
    {
        try {
            $reports = $this->smsApi->getOutboundSmsMessageDeliveryReports(
                bulkId: $bulkId,
                messageId: $messageId,
                limit: 100
            );

            return [
                'success' => true,
                'reports' => $reports->getResults() ?? [],
                'message' => 'تم جلب التقارير بنجاح'
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'فشل جلب التقارير'
            ];
        }
    }

    /**
     * الحصول على الرسائل الواردة
     *
     * @return array
     */
    public function getInboundMessages(): array
    {
        try {
            $messages = $this->smsApi->getInboundSmsMessages(limit: 100);

            return [
                'success' => true,
                'messages' => $messages->getResults() ?? [],
                'message' => 'تم جلب الرسائل الواردة بنجاح'
            ];
        } catch (ApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'فشل جلب الرسائل الواردة'
            ];
        }
    }
}
