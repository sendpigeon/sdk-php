<?php

declare(strict_types=1);

namespace SendPigeon;

// Webhook event types
const WEBHOOK_EVENT_DELIVERED = 'email.delivered';
const WEBHOOK_EVENT_BOUNCED = 'email.bounced';
const WEBHOOK_EVENT_COMPLAINED = 'email.complained';
const WEBHOOK_EVENT_OPENED = 'email.opened';
const WEBHOOK_EVENT_CLICKED = 'email.clicked';
const WEBHOOK_EVENT_TEST = 'webhook.test';

readonly class WebhookPayloadData
{
    public function __construct(
        public ?string $emailId = null,
        public ?string $toAddress = null,
        public ?string $fromAddress = null,
        public ?string $subject = null,
        public ?string $bounceType = null,
        public ?string $complaintType = null,
        // Present for email.opened events
        public ?string $openedAt = null,
        // Present for email.clicked events
        public ?string $clickedAt = null,
        public ?string $linkUrl = null,
        public ?int $linkIndex = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            emailId: $data['emailId'] ?? null,
            toAddress: $data['toAddress'] ?? null,
            fromAddress: $data['fromAddress'] ?? null,
            subject: $data['subject'] ?? null,
            bounceType: $data['bounceType'] ?? null,
            complaintType: $data['complaintType'] ?? null,
            openedAt: $data['openedAt'] ?? null,
            clickedAt: $data['clickedAt'] ?? null,
            linkUrl: $data['linkUrl'] ?? null,
            linkIndex: $data['linkIndex'] ?? null,
        );
    }
}

readonly class WebhookPayload
{
    public function __construct(
        public string $event,
        public string $timestamp,
        public WebhookPayloadData $data,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            event: $payload['event'] ?? '',
            timestamp: $payload['timestamp'] ?? '',
            data: WebhookPayloadData::fromArray($payload['data'] ?? []),
        );
    }
}

readonly class WebhookVerifyResult
{
    public function __construct(
        public bool $valid,
        public ?array $payload = null,
        public ?string $error = null,
    ) {}

    /**
     * Get typed payload. Returns null if verification failed.
     */
    public function getTypedPayload(): ?WebhookPayload
    {
        if (!$this->valid || $this->payload === null) {
            return null;
        }
        return WebhookPayload::fromArray($this->payload);
    }
}

class Webhooks
{
    /**
     * Verify a webhook signature from SendPigeon.
     *
     * @param string $payload Raw request body
     * @param string $signature Value of X-Webhook-Signature header
     * @param string $timestamp Value of X-Webhook-Timestamp header
     * @param string $secret Your webhook secret from dashboard
     * @param int $maxAge Maximum age of webhook in seconds (default: 300)
     *
     * @example
     * $result = Webhooks::verify(
     *     payload: file_get_contents('php://input'),
     *     signature: $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? '',
     *     timestamp: $_SERVER['HTTP_X_WEBHOOK_TIMESTAMP'] ?? '',
     *     secret: 'whsec_xxx',
     * );
     * if ($result->valid) {
     *     handleEvent($result->payload);
     * }
     */
    public static function verify(
        string $payload,
        string $signature,
        string $timestamp,
        string $secret,
        int $maxAge = 300,
    ): WebhookVerifyResult {
        // Validate timestamp
        if (!is_numeric($timestamp)) {
            return new WebhookVerifyResult(false, error: 'Invalid timestamp');
        }

        $ts = (int) $timestamp;
        $now = time();

        if (abs($now - $ts) > $maxAge) {
            return new WebhookVerifyResult(false, error: 'Timestamp too old');
        }

        // Compute expected signature
        $signedPayload = "{$timestamp}.{$payload}";
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        // Timing-safe comparison
        if (!hash_equals($expected, $signature)) {
            return new WebhookVerifyResult(false, error: 'Invalid signature');
        }

        // Parse payload
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WebhookVerifyResult(false, error: 'Invalid JSON payload');
        }

        return new WebhookVerifyResult(true, payload: $data);
    }

    /**
     * Verify an inbound email webhook signature.
     * Same verification logic as regular webhooks.
     */
    public static function verifyInbound(
        string $payload,
        string $signature,
        string $timestamp,
        string $secret,
        int $maxAge = 300,
    ): WebhookVerifyResult {
        return self::verify($payload, $signature, $timestamp, $secret, $maxAge);
    }
}
