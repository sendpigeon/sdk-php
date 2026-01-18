<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class Contact
{
    public function __construct(
        public string $id,
        public string $email,
        public ContactStatus $status,
        public string $createdAt,
        public string $updatedAt,
        public array $fields = [],
        public array $tags = [],
        public ?string $timezone = null,
        public ?string $unsubscribedAt = null,
        public ?string $bouncedAt = null,
        public ?string $complainedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
            status: ContactStatus::from($data['status']),
            createdAt: $data['createdAt'],
            updatedAt: $data['updatedAt'],
            fields: $data['fields'] ?? [],
            tags: $data['tags'] ?? [],
            timezone: $data['timezone'] ?? null,
            unsubscribedAt: $data['unsubscribedAt'] ?? null,
            bouncedAt: $data['bouncedAt'] ?? null,
            complainedAt: $data['complainedAt'] ?? null,
        );
    }
}
