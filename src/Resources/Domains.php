<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\Domain;
use SendPigeon\Types\DomainVerificationResult;

class Domains
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * Add a new domain.
     */
    public function create(string $name): Domain
    {
        $response = $this->http->post('/v1/domains', ['name' => $name]);
        return Domain::fromArray($response);
    }

    /**
     * Get a domain by ID.
     */
    public function get(string $id): Domain
    {
        $response = $this->http->get("/v1/domains/{$id}");
        return Domain::fromArray($response);
    }

    /**
     * List all domains.
     *
     * @return array{data: Domain[], cursor?: array{next?: string}}
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

        $path = '/v1/domains';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => Domain::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Trigger domain verification.
     */
    public function verify(string $id): DomainVerificationResult
    {
        $response = $this->http->post("/v1/domains/{$id}/verify");
        return DomainVerificationResult::fromArray($response);
    }

    /**
     * Delete a domain.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/domains/{$id}");
    }
}
