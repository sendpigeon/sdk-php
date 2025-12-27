<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\Template;

class Templates
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * Create a new template.
     */
    public function create(
        string $name,
        string $subject,
        ?string $html = null,
        ?string $text = null,
        ?string $domainId = null,
    ): Template {
        $body = [
            'name' => $name,
            'subject' => $subject,
        ];

        if ($html !== null) $body['html'] = $html;
        if ($text !== null) $body['text'] = $text;
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
     */
    public function update(
        string $id,
        ?string $name = null,
        ?string $subject = null,
        ?string $html = null,
        ?string $text = null,
    ): Template {
        $body = [];
        if ($name !== null) $body['name'] = $name;
        if ($subject !== null) $body['subject'] = $subject;
        if ($html !== null) $body['html'] = $html;
        if ($text !== null) $body['text'] = $text;

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
}
