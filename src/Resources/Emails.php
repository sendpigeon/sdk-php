<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\EmailDetail;

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
     * Cancel a scheduled email.
     */
    public function cancel(string $id): EmailDetail
    {
        $response = $this->http->post("/v1/emails/{$id}/cancel");
        return EmailDetail::fromArray($response);
    }
}
