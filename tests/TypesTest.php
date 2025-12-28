<?php

declare(strict_types=1);

namespace SendPigeon\Tests;

use PHPUnit\Framework\TestCase;
use SendPigeon\Types\ApiKey;
use SendPigeon\Types\ApiKeyMode;
use SendPigeon\Types\ApiKeyPermission;
use SendPigeon\Types\Domain;
use SendPigeon\Types\DomainStatus;
use SendPigeon\Types\EmailDetail;
use SendPigeon\Types\EmailStatus;
use SendPigeon\Types\SendEmailResponse;
use SendPigeon\Types\Template;

class TypesTest extends TestCase
{
    public function testSendEmailResponseFromArray(): void
    {
        $data = [
            'id' => 'email_123',
            'status' => 'pending',
            'scheduled_at' => '2024-01-15T10:00:00Z',
            'suppressed' => ['blocked@example.com'],
        ];

        $response = SendEmailResponse::fromArray($data);

        $this->assertEquals('email_123', $response->id);
        $this->assertEquals(EmailStatus::Pending, $response->status);
        $this->assertEquals('2024-01-15T10:00:00Z', $response->scheduledAt);
        $this->assertEquals(['blocked@example.com'], $response->suppressed);
    }

    public function testEmailDetailFromArray(): void
    {
        $data = [
            'id' => 'email_123',
            'from_address' => 'sender@example.com',
            'to_address' => 'recipient@example.com',
            'subject' => 'Test Subject',
            'status' => 'delivered',
            'created_at' => '2024-01-01T00:00:00Z',
            'tags' => ['welcome', 'onboarding'],
        ];

        $detail = EmailDetail::fromArray($data);

        $this->assertEquals('email_123', $detail->id);
        $this->assertEquals('sender@example.com', $detail->fromAddress);
        $this->assertEquals(EmailStatus::Delivered, $detail->status);
        $this->assertEquals(['welcome', 'onboarding'], $detail->tags);
    }

    public function testTemplateFromArray(): void
    {
        $data = [
            'id' => 'tmpl_123',
            'templateId' => 'welcome',
            'name' => 'Welcome Email',
            'subject' => 'Welcome, {{name}}!',
            'variables' => [
                ['key' => 'name', 'type' => 'string'],
                ['key' => 'company', 'type' => 'string', 'fallbackValue' => 'Acme'],
            ],
            'status' => 'draft',
            'createdAt' => '2024-01-01T00:00:00Z',
            'updatedAt' => '2024-01-02T00:00:00Z',
            'html' => '<h1>Hello {{name}}</h1>',
        ];

        $template = Template::fromArray($data);

        $this->assertEquals('tmpl_123', $template->id);
        $this->assertEquals('welcome', $template->templateId);
        $this->assertEquals('Welcome Email', $template->name);
        $this->assertEquals('draft', $template->status);
        $this->assertCount(2, $template->variables);
        $this->assertEquals('name', $template->variables[0]->key);
        $this->assertEquals('string', $template->variables[0]->type);
        $this->assertEquals('Acme', $template->variables[1]->fallbackValue);
        $this->assertEquals('<h1>Hello {{name}}</h1>', $template->html);
    }

    public function testDomainFromArray(): void
    {
        $data = [
            'id' => 'dom_123',
            'name' => 'mail.example.com',
            'status' => 'verified',
            'created_at' => '2024-01-01T00:00:00Z',
            'verified_at' => '2024-01-02T00:00:00Z',
            'dns_records' => [
                ['type' => 'TXT', 'name' => '_dmarc', 'value' => 'v=DMARC1'],
            ],
        ];

        $domain = Domain::fromArray($data);

        $this->assertEquals('dom_123', $domain->id);
        $this->assertEquals(DomainStatus::Verified, $domain->status);
        $this->assertCount(1, $domain->dnsRecords);
        $this->assertEquals('TXT', $domain->dnsRecords[0]->type);
    }

    public function testApiKeyFromArray(): void
    {
        $data = [
            'id' => 'key_123',
            'name' => 'Production',
            'key_prefix' => 'sk_live_xxx',
            'mode' => 'live',
            'permission' => 'full_access',
            'created_at' => '2024-01-01T00:00:00Z',
            'key' => 'sk_live_full_key',
        ];

        $apiKey = ApiKey::fromArray($data);

        $this->assertEquals('key_123', $apiKey->id);
        $this->assertEquals(ApiKeyMode::Live, $apiKey->mode);
        $this->assertEquals(ApiKeyPermission::FullAccess, $apiKey->permission);
        $this->assertEquals('sk_live_full_key', $apiKey->key);
    }
}
