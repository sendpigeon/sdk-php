<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class TestBroadcastResponse
{
    public function __construct(
        public string $message,
        public array $emailIds,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            message: $data['message'],
            emailIds: $data['emailIds'] ?? [],
        );
    }
}
