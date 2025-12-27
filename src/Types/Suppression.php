<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class Suppression
{
    public function __construct(
        public string $id,
        public string $email,
        public SuppressionReason $reason,
        public string $createdAt,
        public ?string $sourceEmailId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
            reason: SuppressionReason::from($data['reason']),
            createdAt: $data['createdAt'],
            sourceEmailId: $data['sourceEmailId'] ?? null,
        );
    }
}
