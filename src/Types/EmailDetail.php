<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class EmailDetail
{
    public function __construct(
        public string $id,
        public string $fromAddress,
        public string $toAddress,
        public string $subject,
        public EmailStatus $status,
        public string $createdAt,
        public ?string $ccAddress = null,
        public ?string $bccAddress = null,
        public array $tags = [],
        public ?array $metadata = null,
        public ?string $sentAt = null,
        public ?string $deliveredAt = null,
        public ?string $bouncedAt = null,
        public ?string $complainedAt = null,
        public ?string $bounceType = null,
        public ?string $complaintType = null,
        public ?array $attachments = null,
        public bool $hasBody = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            fromAddress: $data['from_address'],
            toAddress: $data['to_address'],
            subject: $data['subject'],
            status: EmailStatus::from($data['status']),
            createdAt: $data['created_at'],
            ccAddress: $data['cc_address'] ?? null,
            bccAddress: $data['bcc_address'] ?? null,
            tags: $data['tags'] ?? [],
            metadata: $data['metadata'] ?? null,
            sentAt: $data['sent_at'] ?? null,
            deliveredAt: $data['delivered_at'] ?? null,
            bouncedAt: $data['bounced_at'] ?? null,
            complainedAt: $data['complained_at'] ?? null,
            bounceType: $data['bounce_type'] ?? null,
            complaintType: $data['complaint_type'] ?? null,
            attachments: $data['attachments'] ?? null,
            hasBody: $data['has_body'] ?? false,
        );
    }
}
