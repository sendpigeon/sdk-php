<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\EmailDetail;
use SendPigeon\Types\EmailStatus;

class Emails
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * Get an email by ID.
     */
    public function get(string $id): EmailDetail
    {
        $response = $this->http->get("/v1/emails/{$id}");
        return EmailDetail::fromArray($response);
    }

    /**
     * List emails with optional filters.
     *
     * @return array{data: EmailDetail[], cursor?: array{next?: string}}
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null,
        ?EmailStatus $status = null,
        ?string $tag = null,
    ): array {
        $params = [];
        if ($limit !== null) $params['limit'] = $limit;
        if ($offset !== null) $params['offset'] = $offset;
        if ($cursor !== null) $params['cursor'] = $cursor;
        if ($status !== null) $params['status'] = $status->value;
        if ($tag !== null) $params['tag'] = $tag;

        $path = '/v1/emails';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => EmailDetail::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Cancel a scheduled email.
     */
    public function cancel(string $id): EmailDetail
    {
        $response = $this->http->post("/v1/emails/{$id}/cancel");
        return EmailDetail::fromArray($response);
    }
}
