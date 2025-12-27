<?php

declare(strict_types=1);

namespace SendPigeon\Tests;

use PHPUnit\Framework\TestCase;
use SendPigeon\Webhooks;

class WebhooksTest extends TestCase
{
    private function sign(string $payload, string $secret, int $timestamp): string
    {
        $signedPayload = "{$timestamp}.{$payload}";
        return hash_hmac('sha256', $signedPayload, $secret);
    }

    public function testVerifyValidWebhook(): void
    {
        $secret = 'whsec_test123';
        $payload = '{"type":"email.delivered","data":{"id":"email_123"}}';
        $timestamp = time();
        $signature = $this->sign($payload, $secret, $timestamp);

        $result = Webhooks::verify($payload, $signature, (string) $timestamp, $secret);

        $this->assertTrue($result->valid);
        $this->assertNull($result->error);
        $this->assertEquals('email.delivered', $result->payload['type']);
    }

    public function testVerifyInvalidSignature(): void
    {
        $secret = 'whsec_test123';
        $payload = '{"type":"email.delivered"}';
        $timestamp = time();

        $result = Webhooks::verify($payload, 'invalid_signature', (string) $timestamp, $secret);

        $this->assertFalse($result->valid);
        $this->assertEquals('Invalid signature', $result->error);
    }

    public function testVerifyExpiredTimestamp(): void
    {
        $secret = 'whsec_test123';
        $payload = '{"type":"email.delivered"}';
        $timestamp = time() - 600; // 10 minutes ago
        $signature = $this->sign($payload, $secret, $timestamp);

        $result = Webhooks::verify($payload, $signature, (string) $timestamp, $secret, 300);

        $this->assertFalse($result->valid);
        $this->assertEquals('Timestamp too old', $result->error);
    }

    public function testVerifyInvalidTimestamp(): void
    {
        $secret = 'whsec_test123';
        $payload = '{"type":"email.delivered"}';

        $result = Webhooks::verify($payload, 'signature', 'not-a-number', $secret);

        $this->assertFalse($result->valid);
        $this->assertEquals('Invalid timestamp', $result->error);
    }

    public function testVerifyInvalidJson(): void
    {
        $secret = 'whsec_test123';
        $payload = 'not json';
        $timestamp = time();
        $signature = $this->sign($payload, $secret, $timestamp);

        $result = Webhooks::verify($payload, $signature, (string) $timestamp, $secret);

        $this->assertFalse($result->valid);
        $this->assertEquals('Invalid JSON payload', $result->error);
    }

    public function testVerifyInboundWebhook(): void
    {
        $secret = 'whsec_inbound123';
        $payload = '{"type":"email.received","data":{"from":"sender@example.com"}}';
        $timestamp = time();
        $signature = $this->sign($payload, $secret, $timestamp);

        $result = Webhooks::verifyInbound($payload, $signature, (string) $timestamp, $secret);

        $this->assertTrue($result->valid);
        $this->assertEquals('email.received', $result->payload['type']);
    }
}
