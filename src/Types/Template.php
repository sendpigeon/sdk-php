<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class Template
{
    /**
     * @param TemplateVariable[] $variables
     */
    public function __construct(
        public string $id,
        public string $templateId,
        public string $subject,
        public array $variables,
        public string $status,
        public string $createdAt,
        public string $updatedAt,
        public ?string $name = null,
        public ?string $html = null,
        public ?string $text = null,
        public ?array $domain = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $variables = array_map(
            fn(array $v) => TemplateVariable::fromArray($v),
            $data['variables'] ?? []
        );

        return new self(
            id: $data['id'],
            templateId: $data['templateId'],
            subject: $data['subject'],
            variables: $variables,
            status: $data['status'],
            createdAt: $data['createdAt'],
            updatedAt: $data['updatedAt'],
            name: $data['name'] ?? null,
            html: $data['html'] ?? null,
            text: $data['text'] ?? null,
            domain: $data['domain'] ?? null,
        );
    }
}
