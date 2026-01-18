<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class Broadcast
{
    public function __construct(
        public string $id,
        public string $name,
        public string $subject,
        public BroadcastStatus $status,
        public string $createdAt,
        public string $updatedAt,
        public ?string $previewText = null,
        public ?string $fromEmail = null,
        public ?string $fromName = null,
        public ?string $replyTo = null,
        public ?string $htmlContent = null,
        public ?string $textContent = null,
        public array $tags = [],
        public ?BroadcastStats $stats = null,
        public ?string $scheduledAt = null,
        public ?string $sentAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            subject: $data['subject'],
            status: BroadcastStatus::from($data['status']),
            createdAt: $data['createdAt'],
            updatedAt: $data['updatedAt'],
            previewText: $data['previewText'] ?? null,
            fromEmail: $data['fromEmail'] ?? null,
            fromName: $data['fromName'] ?? null,
            replyTo: $data['replyTo'] ?? null,
            htmlContent: $data['htmlContent'] ?? null,
            textContent: $data['textContent'] ?? null,
            tags: $data['tags'] ?? [],
            stats: isset($data['stats']) ? BroadcastStats::fromArray($data['stats']) : null,
            scheduledAt: $data['scheduledAt'] ?? null,
            sentAt: $data['sentAt'] ?? null,
        );
    }
}
