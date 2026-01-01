<?php

declare(strict_types=1);

namespace SendPigeon;

use SendPigeon\Resources\ApiKeys;
use SendPigeon\Resources\Domains;
use SendPigeon\Resources\Emails;
use SendPigeon\Resources\Suppressions;
use SendPigeon\Resources\Templates;
use SendPigeon\Resources\Tracking;
use SendPigeon\Types\SendBatchResponse;
use SendPigeon\Types\SendEmailResponse;
use SendPigeon\Types\TrackingOptions;

/**
 * SendPigeon SDK client for sending transactional emails.
 *
 * @example
 * $client = new Client('sk_live_xxx');
 * $response = $client->send(
 *     to: 'user@example.com',
 *     subject: 'Hello',
 *     html: '<p>Hi there!</p>'
 * );
 */
class Client
{
    private HttpClient $http;

    public readonly Emails $emails;
    public readonly Templates $templates;
    public readonly Domains $domains;
    public readonly ApiKeys $apiKeys;
    public readonly Suppressions $suppressions;
    public readonly Tracking $tracking;

    /**
     * Create a new Client.
     *
     * @param string $apiKey Your SendPigeon API key (sk_live_xxx or sk_test_xxx)
     * @param string|null $baseUrl Override the API base URL
     * @param int|null $timeout Request timeout in seconds (default: 30)
     * @param int|null $maxRetries Max retry attempts (default: 2, max: 5)
     */
    public function __construct(
        string $apiKey,
        ?string $baseUrl = null,
        ?int $timeout = null,
        ?int $maxRetries = null,
    ) {
        $this->http = new HttpClient($apiKey, $baseUrl, $timeout, $maxRetries);
        $this->emails = new Emails($this->http);
        $this->templates = new Templates($this->http);
        $this->domains = new Domains($this->http);
        $this->apiKeys = new ApiKeys($this->http);
        $this->suppressions = new Suppressions($this->http);
        $this->tracking = new Tracking($this->http);
    }

    /**
     * Send a transactional email.
     *
     * @param string|array $to Recipient email address(es)
     * @param string|null $subject Email subject (required unless using templateId)
     * @param string|null $html HTML body content
     * @param string|null $text Plain text body content
     * @param string|null $from Sender email address
     * @param string|array|null $cc CC recipient(s)
     * @param string|array|null $bcc BCC recipient(s)
     * @param string|null $replyTo Reply-to address
     * @param string|null $templateId Template ID to use
     * @param array|null $variables Variables to substitute in template
     * @param array|null $attachments File attachments
     * @param array|null $tags Tags for filtering (max 5)
     * @param array|null $metadata Custom key-value pairs
     * @param array|null $headers Custom email headers
     * @param string|null $scheduledAt ISO datetime to send
     * @param string|null $idempotencyKey Unique key to prevent duplicates
     * @param TrackingOptions|null $tracking Per-email tracking options
     */
    public function send(
        string|array $to,
        ?string $subject = null,
        ?string $html = null,
        ?string $text = null,
        ?string $from = null,
        string|array|null $cc = null,
        string|array|null $bcc = null,
        ?string $replyTo = null,
        ?string $templateId = null,
        ?array $variables = null,
        ?array $attachments = null,
        ?array $tags = null,
        ?array $metadata = null,
        ?array $headers = null,
        ?string $scheduledAt = null,
        ?string $idempotencyKey = null,
        ?TrackingOptions $tracking = null,
    ): SendEmailResponse {
        $body = array_filter([
            'to' => is_array($to) ? $to : [$to],
            'from' => $from,
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
            'cc' => $cc !== null ? (is_array($cc) ? $cc : [$cc]) : null,
            'bcc' => $bcc !== null ? (is_array($bcc) ? $bcc : [$bcc]) : null,
            'replyTo' => $replyTo,
            'templateId' => $templateId,
            'variables' => $variables,
            'attachments' => $attachments,
            'tags' => $tags,
            'metadata' => $metadata,
            'headers' => $headers,
            'scheduled_at' => $scheduledAt,
            'tracking' => $tracking?->toArray(),
        ], fn($v) => $v !== null);

        $requestHeaders = $idempotencyKey !== null ? ['Idempotency-Key' => $idempotencyKey] : [];

        $response = $this->http->post('/v1/emails', $body, $requestHeaders);
        return SendEmailResponse::fromArray($response);
    }

    /**
     * Send multiple emails in a single request (max 100).
     *
     * @param array $emails Array of email objects
     */
    public function sendBatch(array $emails): SendBatchResponse
    {
        $apiEmails = array_map(function (array $email): array {
            $to = $email['to'] ?? null;
            $tracking = $email['tracking'] ?? null;
            return array_filter([
                'to' => $to !== null ? (is_array($to) ? $to : [$to]) : null,
                'from' => $email['from'] ?? null,
                'subject' => $email['subject'] ?? null,
                'html' => $email['html'] ?? null,
                'text' => $email['text'] ?? null,
                'cc' => $email['cc'] ?? null,
                'bcc' => $email['bcc'] ?? null,
                'replyTo' => $email['replyTo'] ?? null,
                'templateId' => $email['templateId'] ?? null,
                'variables' => $email['variables'] ?? null,
                'tags' => $email['tags'] ?? null,
                'metadata' => $email['metadata'] ?? null,
                'headers' => $email['headers'] ?? null,
                'scheduled_at' => $email['scheduledAt'] ?? null,
                'tracking' => $tracking instanceof TrackingOptions ? $tracking->toArray() : $tracking,
            ], fn($v) => $v !== null);
        }, $emails);

        $response = $this->http->post('/v1/emails/batch', ['emails' => $apiEmails]);
        return SendBatchResponse::fromArray($response);
    }
}
