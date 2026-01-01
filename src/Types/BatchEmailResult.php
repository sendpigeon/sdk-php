<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class BatchEmailResult
{
    public function __construct(
        public int $index,
        public string $status,
        public ?string $id = null,
        public ?array $suppressed = null,
        public ?array $warnings = null,
        public ?array $error = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            index: $data['index'],
            status: $data['status'],
            id: $data['id'] ?? null,
            suppressed: $data['suppressed'] ?? null,
            warnings: $data['warnings'] ?? null,
            error: $data['error'] ?? null,
        );
    }
}
