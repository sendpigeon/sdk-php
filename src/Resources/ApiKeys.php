<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\ApiKey;
use SendPigeon\Types\ApiKeyMode;
use SendPigeon\Types\ApiKeyPermission;

class ApiKeys
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * Create a new API key.
     */
    public function create(
        string $name,
        ?ApiKeyMode $mode = null,
        ?ApiKeyPermission $permission = null,
        ?string $domainId = null,
        ?string $expiresAt = null,
    ): ApiKey {
        $body = ['name' => $name];

        if ($mode !== null) $body['mode'] = $mode->value;
        if ($permission !== null) $body['permission'] = $permission->value;
        if ($domainId !== null) $body['domainId'] = $domainId;
        if ($expiresAt !== null) $body['expiresAt'] = $expiresAt;

        $response = $this->http->post('/v1/api-keys', $body);
        return ApiKey::fromArray($response);
    }

    /**
     * Get an API key by ID.
     */
    public function get(string $id): ApiKey
    {
        $response = $this->http->get("/v1/api-keys/{$id}");
        return ApiKey::fromArray($response);
    }

    /**
     * List all API keys.
     *
     * @return array{data: ApiKey[], cursor?: array{next?: string}}
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

        $path = '/v1/api-keys';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => ApiKey::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Delete (revoke) an API key.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/api-keys/{$id}");
    }
}
