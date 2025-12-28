<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class TestTemplateResponse
{
    public function __construct(
        public string $message,
        public string $emailId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            message: $data['message'],
            emailId: $data['emailId'],
        );
    }
}
