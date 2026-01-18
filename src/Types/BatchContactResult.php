<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class BatchContactResult
{
    public function __construct(
        public int $index,
        public string $status,
        public ?string $id = null,
        public ?string $email = null,
        public ?string $error = null,
        public ?string $message = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            index: $data['index'],
            status: $data['status'],
            id: $data['id'] ?? null,
            email: $data['email'] ?? null,
            error: $data['error'] ?? null,
            message: $data['message'] ?? null,
        );
    }
}
