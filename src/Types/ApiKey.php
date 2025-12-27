<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class ApiKey
{
    public function __construct(
        public string $id,
        public string $name,
        public string $keyPrefix,
        public ApiKeyMode $mode,
        public ApiKeyPermission $permission,
        public string $createdAt,
        public ?string $lastUsedAt = null,
        public ?string $expiresAt = null,
        public ?array $domain = null,
        public ?string $key = null, // Only set on creation
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            keyPrefix: $data['key_prefix'],
            mode: ApiKeyMode::from($data['mode']),
            permission: ApiKeyPermission::from($data['permission']),
            createdAt: $data['created_at'],
            lastUsedAt: $data['last_used_at'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            domain: $data['domain'] ?? null,
            key: $data['key'] ?? null,
        );
    }
}
