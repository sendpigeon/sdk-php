<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\Template;
use SendPigeon\Types\TestTemplateResponse;

class Templates
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * Create a new template.
     *
     * @param array<array{key: string, type: string, fallbackValue?: string}>|null $variables
     */
    public function create(
        string $templateId,
        string $subject,
        ?string $name = null,
        ?string $html = null,
        ?string $text = null,
        ?array $variables = null,
        ?string $domainId = null,
    ): Template {
        $body = [
            'templateId' => $templateId,
            'subject' => $subject,
        ];

        if ($name !== null) $body['name'] = $name;
        if ($html !== null) $body['html'] = $html;
        if ($text !== null) $body['text'] = $text;
        if ($variables !== null) $body['variables'] = $variables;
        if ($domainId !== null) $body['domainId'] = $domainId;

        $response = $this->http->post('/v1/templates', $body);
        return Template::fromArray($response);
    }

    /**
     * Get a template by ID.
     */
    public function get(string $id): Template
    {
        $response = $this->http->get("/v1/templates/{$id}");
        return Template::fromArray($response);
    }

    /**
     * List all templates.
     *
     * @return array{data: Template[], cursor?: array{next?: string}}
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null,
    ): array {
        $params = [];
        if ($limit !== null) $params['limit'] = $limit;
        if ($offset !== null) $params['offset'] = $offset;
        if ($cursor !== null) $params['cursor'] = $cursor;

        $path = '/v1/templates';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => Template::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Update a template.
     *
     * @param array<array{key: string, type: string, fallbackValue?: string}>|null $variables
     */
    public function update(
        string $id,
        ?string $name = null,
        ?string $subject = null,
        ?string $html = null,
        ?string $text = null,
        ?array $variables = null,
    ): Template {
        $body = [];
        if ($name !== null) $body['name'] = $name;
        if ($subject !== null) $body['subject'] = $subject;
        if ($html !== null) $body['html'] = $html;
        if ($text !== null) $body['text'] = $text;
        if ($variables !== null) $body['variables'] = $variables;

        $response = $this->http->patch("/v1/templates/{$id}", $body);
        return Template::fromArray($response);
    }

    /**
     * Delete a template.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/templates/{$id}");
    }

    /**
     * Publish a template.
     */
    public function publish(string $id): Template
    {
        $response = $this->http->post("/v1/templates/{$id}/publish", []);
        return Template::fromArray($response);
    }

    /**
     * Unpublish a template.
     */
    public function unpublish(string $id): Template
    {
        $response = $this->http->post("/v1/templates/{$id}/unpublish", []);
        return Template::fromArray($response);
    }

    /**
     * Send a test email using the template.
     *
     * @param array<string, string>|null $variables
     */
    public function test(
        string $id,
        string $to,
        ?array $variables = null,
    ): TestTemplateResponse {
        $body = ['to' => $to];
        if ($variables !== null) $body['variables'] = $variables;

        $response = $this->http->post("/v1/templates/{$id}/test", $body);
        return TestTemplateResponse::fromArray($response);
    }
}
