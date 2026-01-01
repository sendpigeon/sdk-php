<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class SendEmailResponse
{
    public function __construct(
        public string $id,
        public EmailStatus $status,
        public ?string $scheduledAt = null,
        public ?array $suppressed = null,
        public ?array $warnings = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: EmailStatus::from($data['status']),
            scheduledAt: $data['scheduled_at'] ?? null,
            suppressed: $data['suppressed'] ?? null,
            warnings: $data['warnings'] ?? null,
        );
    }
}
