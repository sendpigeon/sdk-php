<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class TemplateVariable
{
    public function __construct(
        public string $key,
        public string $type,
        public ?string $fallbackValue = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            type: $data['type'],
            fallbackValue: $data['fallbackValue'] ?? null,
        );
    }
}
