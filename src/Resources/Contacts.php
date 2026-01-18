<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\AudienceStats;
use SendPigeon\Types\BatchContactResult;
use SendPigeon\Types\Contact;

class Contacts
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * List contacts with optional filtering.
     *
     * @return array{data: Contact[], cursor?: array{next?: string}}
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null,
        ?array $tags = null,
        ?string $status = null,
        ?string $search = null,
    ): array {
        $params = [];
        if ($limit !== null) $params['limit'] = $limit;
        if ($offset !== null) $params['offset'] = $offset;
        if ($cursor !== null) $params['cursor'] = $cursor;
        if ($tags !== null) $params['tags'] = implode(',', $tags);
        if ($status !== null) $params['status'] = $status;
        if ($search !== null) $params['search'] = $search;

        $path = '/v1/contacts';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => Contact::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Get audience statistics.
     */
    public function stats(): AudienceStats
    {
        $response = $this->http->get('/v1/contacts/stats');
        return AudienceStats::fromArray($response);
    }

    /**
     * Get all unique tags.
     *
     * @return string[]
     */
    public function tags(): array
    {
        $response = $this->http->get('/v1/contacts/tags');
        return $response['data'] ?? [];
    }

    /**
     * Create a new contact.
     */
    public function create(
        string $email,
        ?array $fields = null,
        ?array $tags = null,
        ?string $timezone = null,
    ): Contact {
        $body = array_filter([
            'email' => $email,
            'fields' => $fields,
            'tags' => $tags,
            'timezone' => $timezone,
        ], fn($v) => $v !== null);

        $response = $this->http->post('/v1/contacts', $body);
        return Contact::fromArray($response);
    }

    /**
     * Create or update multiple contacts.
     *
     * @param array $contacts Array of contact data
     * @return array{data: BatchContactResult[], summary: array{total: int, created: int, updated: int, failed: int}}
     */
    public function batch(array $contacts): array
    {
        $response = $this->http->post('/v1/contacts/batch', ['contacts' => $contacts]);

        return [
            'data' => array_map(
                fn(array $item) => BatchContactResult::fromArray($item),
                $response['data'] ?? []
            ),
            'summary' => $response['summary'] ?? [],
        ];
    }

    /**
     * Get a contact by ID.
     */
    public function get(string $id): Contact
    {
        $response = $this->http->get("/v1/contacts/{$id}");
        return Contact::fromArray($response);
    }

    /**
     * Update a contact.
     */
    public function update(
        string $id,
        ?string $email = null,
        ?array $fields = null,
        ?array $tags = null,
        ?string $timezone = null,
    ): Contact {
        $body = array_filter([
            'email' => $email,
            'fields' => $fields,
            'tags' => $tags,
            'timezone' => $timezone,
        ], fn($v) => $v !== null);

        $response = $this->http->patch("/v1/contacts/{$id}", $body);
        return Contact::fromArray($response);
    }

    /**
     * Delete a contact.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/contacts/{$id}");
    }

    /**
     * Unsubscribe a contact.
     */
    public function unsubscribe(string $id): Contact
    {
        $response = $this->http->post("/v1/contacts/{$id}/unsubscribe");
        return Contact::fromArray($response);
    }

    /**
     * Resubscribe a contact.
     */
    public function resubscribe(string $id): Contact
    {
        $response = $this->http->post("/v1/contacts/{$id}/resubscribe");
        return Contact::fromArray($response);
    }
}
