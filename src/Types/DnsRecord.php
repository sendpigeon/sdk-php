<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class DnsRecord
{
    public function __construct(
        public string $type,
        public string $name,
        public string $value,
        public ?int $priority = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            name: $data['name'],
            value: $data['value'],
            priority: $data['priority'] ?? null,
        );
    }
}
