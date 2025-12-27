<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class Template
{
    public function __construct(
        public string $id,
        public string $name,
        public string $subject,
        public array $variables,
        public string $createdAt,
        public string $updatedAt,
        public ?string $html = null,
        public ?string $text = null,
        public ?array $domain = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            subject: $data['subject'],
            variables: $data['variables'] ?? [],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
            html: $data['html'] ?? null,
            text: $data['text'] ?? null,
            domain: $data['domain'] ?? null,
        );
    }
}
