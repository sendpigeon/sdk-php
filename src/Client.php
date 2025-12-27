<?php

declare(strict_types=1);

namespace SendPigeon;

use SendPigeon\Resources\ApiKeys;
use SendPigeon\Resources\Domains;
use SendPigeon\Resources\Emails;
use SendPigeon\Resources\Templates;
use SendPigeon\Types\SendBatchResponse;
use SendPigeon\Types\SendEmailResponse;

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
    ): SendEmailResponse {
        $body = ['to' => is_array($to) ? $to : [$to]];

        if ($from !== null) $body['from'] = $from;
        if ($subject !== null) $body['subject'] = $subject;
        if ($html !== null) $body['html'] = $html;
        if ($text !== null) $body['text'] = $text;
        if ($cc !== null) $body['cc'] = is_array($cc) ? $cc : [$cc];
        if ($bcc !== null) $body['bcc'] = is_array($bcc) ? $bcc : [$bcc];
        if ($replyTo !== null) $body['replyTo'] = $replyTo;
        if ($templateId !== null) $body['templateId'] = $templateId;
        if ($variables !== null) $body['variables'] = $variables;
        if ($attachments !== null) $body['attachments'] = $attachments;
        if ($tags !== null) $body['tags'] = $tags;
        if ($metadata !== null) $body['metadata'] = $metadata;
        if ($headers !== null) $body['headers'] = $headers;
        if ($scheduledAt !== null) $body['scheduled_at'] = $scheduledAt;

        $requestHeaders = [];
        if ($idempotencyKey !== null) {
            $requestHeaders['Idempotency-Key'] = $idempotencyKey;
        }

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
        $apiEmails = array_map(function (array $email) {
            $apiEmail = [];

            if (isset($email['to'])) {
                $apiEmail['to'] = is_array($email['to']) ? $email['to'] : [$email['to']];
            }
            if (isset($email['from'])) $apiEmail['from'] = $email['from'];
            if (isset($email['subject'])) $apiEmail['subject'] = $email['subject'];
            if (isset($email['html'])) $apiEmail['html'] = $email['html'];
            if (isset($email['text'])) $apiEmail['text'] = $email['text'];
            if (isset($email['cc'])) $apiEmail['cc'] = $email['cc'];
            if (isset($email['bcc'])) $apiEmail['bcc'] = $email['bcc'];
            if (isset($email['replyTo'])) $apiEmail['replyTo'] = $email['replyTo'];
            if (isset($email['templateId'])) $apiEmail['templateId'] = $email['templateId'];
            if (isset($email['variables'])) $apiEmail['variables'] = $email['variables'];
            if (isset($email['tags'])) $apiEmail['tags'] = $email['tags'];
            if (isset($email['metadata'])) $apiEmail['metadata'] = $email['metadata'];
            if (isset($email['headers'])) $apiEmail['headers'] = $email['headers'];
            if (isset($email['scheduledAt'])) $apiEmail['scheduled_at'] = $email['scheduledAt'];

            return $apiEmail;
        }, $emails);

        $response = $this->http->post('/v1/emails/batch', ['emails' => $apiEmails]);
        return SendBatchResponse::fromArray($response);
    }
}
