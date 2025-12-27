<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\SuppressionListResponse;

class Suppressions
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * List suppressed email addresses.
     */
    public function list(?int $limit = null, ?int $offset = null): SuppressionListResponse
    {
        $params = [];
        if ($limit !== null) $params['limit'] = $limit;
        if ($offset !== null) $params['offset'] = $offset;

        $path = '/v1/suppressions';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);
        return SuppressionListResponse::fromArray($response);
    }

    /**
     * Remove an email address from the suppression list.
     */
    public function delete(string $email): void
    {
        $this->http->delete('/v1/suppressions/' . rawurlencode($email));
    }
}
