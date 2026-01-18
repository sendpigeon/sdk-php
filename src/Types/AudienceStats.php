<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class AudienceStats
{
    public function __construct(
        public int $total,
        public int $active,
        public int $unsubscribed,
        public int $bounced,
        public int $complained,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            total: $data['total'] ?? 0,
            active: $data['active'] ?? 0,
            unsubscribed: $data['unsubscribed'] ?? 0,
            bounced: $data['bounced'] ?? 0,
            complained: $data['complained'] ?? 0,
        );
    }
}
