<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class BroadcastStats
{
    public function __construct(
        public int $total,
        public int $sent,
        public int $delivered,
        public int $opened,
        public int $clicked,
        public int $bounced,
        public int $complained,
        public int $failed,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            total: $data['total'] ?? 0,
            sent: $data['sent'] ?? 0,
            delivered: $data['delivered'] ?? 0,
            opened: $data['opened'] ?? 0,
            clicked: $data['clicked'] ?? 0,
            bounced: $data['bounced'] ?? 0,
            complained: $data['complained'] ?? 0,
            failed: $data['failed'] ?? 0,
        );
    }
}
