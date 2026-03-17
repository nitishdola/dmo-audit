<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

/**
 * AuditSummaryWhatsAppService
 *
 * Sends the monthly audit summary to leadership via
 * Twilio WhatsApp Content Template API.
 *
 * No image is sent — text-only template message.
 *
 * .env required:
 *   TWILIO_ACCOUNT_SID=ACxxxxxxxx
 *   TWILIO_AUTH_TOKEN=xxxxxxxxxx
 *   TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
 *   TWILIO_TEMPLATE_SID=HXf12902361b6d55bc0746b2f5274ae3c9
 */
class AuditSummaryWhatsAppService
{
    private Client $twilio;
    private string $from;
    private string $templateSid;

    public function __construct()
    {
        $this->twilio      = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from        = config('services.twilio.whatsapp_from');
        $this->templateSid = config('services.twilio.template_sid');
    }

    /**
     * Send the summary to all recipients defined in config/audit_summary.php.
     *
     * @param  array  $stats  From AuditSummaryImageService::collectStats()
     * @return array          ['sent' => [...names], 'failed' => [...]]
     */
    public function broadcast(array $stats): array
    {
        $recipients = config('audit_summary.recipients', []);
        $results    = ['sent' => [], 'failed' => []];
     
        foreach ($recipients as $recipient) {
            try {
                $this->twilio->messages->create(
                    'whatsapp:+' . $recipient['phone'],
                    [
                        'from'             => $this->from,
                        'contentSid'       => $this->templateSid,
                        'contentVariables' => json_encode(
                            $this->variables($recipient, $stats)
                        ),
                    ]
                );

                $results['sent'][] = $recipient['name'];

                Log::info('AuditSummaryWhatsApp: sent', [
                    'to'   => $recipient['name'],
                    'role' => $recipient['role'],
                ]);

            } catch (\Throwable $e) {
                $results['failed'][] = [
                    'name'  => $recipient['name'],
                    'error' => $e->getMessage(),
                ];

                Log::error('AuditSummaryWhatsApp: failed', [
                    'to'    => $recipient['name'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Build the template variable map.
     *
     * These {{1}}–{{9}} slots must match the order defined in your
     * approved Twilio template HXf12902361b6d55bc0746b2f5274ae3c9.
     */
    private function variables(array $recipient, array $stats): array
    {

        $salutation = in_array($recipient['role'], ['ceo', 'jt_ceo'])
            ? "Respected Sir/Ma'am"
            : 'Dear ' . $recipient['name'];

        // return [
        //     '1' => $salutation,
        //     '2' => $stats['from'] . ' – ' . $stats['to'],
        //     '3' => number_format($stats['grand_total']),
        //     '4' => (string) $stats['comp_rate'],
        //     '5' => number_format($stats['tele_total']),
        //     '6' => number_format($stats['field_total']),
        //     '7' => number_format($stats['live_total']),
        //     '8' => number_format($stats['infra_total']),
        //     '9' => number_format($stats['money_charged']),
        // ];

        return [
            '1' => $stats['from'] . ' – ' . $stats['to'],
            '2' => number_format($stats['grand_total']),
            '3' => number_format($stats['tele_total']),
            '4' => number_format($stats['field_total']),
            '5' => number_format($stats['live_total']),
            '6' => number_format($stats['infra_total']),
            '7' => number_format($stats['money_charged']),
            '8' => date('j F, Y'),
        ];
    }
}
