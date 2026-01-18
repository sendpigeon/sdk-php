<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\Broadcast;
use SendPigeon\Types\BroadcastAnalytics;
use SendPigeon\Types\BroadcastRecipient;
use SendPigeon\Types\TestBroadcastResponse;

class Broadcasts
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * List broadcasts with optional filtering.
     *
     * @return array{data: Broadcast[], cursor?: array{next?: string}}
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null,
        ?string $status = null,
    ): array {
        $params = [];
        if ($limit !== null) $params['limit'] = $limit;
        if ($offset !== null) $params['offset'] = $offset;
        if ($cursor !== null) $params['cursor'] = $cursor;
        if ($status !== null) $params['status'] = $status;

        $path = '/v1/broadcasts';
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => Broadcast::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Create a new broadcast.
     */
    public function create(
        string $name,
        string $subject,
        ?string $previewText = null,
        ?string $fromEmail = null,
        ?string $fromName = null,
        ?string $replyTo = null,
        ?string $htmlContent = null,
        ?string $textContent = null,
        ?array $tags = null,
        ?string $templateId = null,
    ): Broadcast {
        $body = array_filter([
            'name' => $name,
            'subject' => $subject,
            'previewText' => $previewText,
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'replyTo' => $replyTo,
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
            'tags' => $tags,
            'templateId' => $templateId,
        ], fn($v) => $v !== null);

        $response = $this->http->post('/v1/broadcasts', $body);
        return Broadcast::fromArray($response);
    }

    /**
     * Get a broadcast by ID.
     */
    public function get(string $id): Broadcast
    {
        $response = $this->http->get("/v1/broadcasts/{$id}");
        return Broadcast::fromArray($response);
    }

    /**
     * Update a broadcast.
     */
    public function update(
        string $id,
        ?string $name = null,
        ?string $subject = null,
        ?string $previewText = null,
        ?string $fromEmail = null,
        ?string $fromName = null,
        ?string $replyTo = null,
        ?string $htmlContent = null,
        ?string $textContent = null,
        ?array $tags = null,
    ): Broadcast {
        $body = array_filter([
            'name' => $name,
            'subject' => $subject,
            'previewText' => $previewText,
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'replyTo' => $replyTo,
            'htmlContent' => $htmlContent,
            'textContent' => $textContent,
            'tags' => $tags,
        ], fn($v) => $v !== null);

        $response = $this->http->patch("/v1/broadcasts/{$id}", $body);
        return Broadcast::fromArray($response);
    }

    /**
     * Delete a broadcast.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/broadcasts/{$id}");
    }

    /**
     * Duplicate a broadcast.
     */
    public function duplicate(string $id): Broadcast
    {
        $response = $this->http->post("/v1/broadcasts/{$id}/duplicate");
        return Broadcast::fromArray($response);
    }

    /**
     * List recipients of a broadcast.
     *
     * @return array{data: BroadcastRecipient[], cursor?: array{next?: string}}
     */
    public function recipients(
        string $id,
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null,
        ?string $status = null,
    ): array {
        $params = [];
        if ($limit !== null) $params['limit'] = $limit;
        if ($offset !== null) $params['offset'] = $offset;
        if ($cursor !== null) $params['cursor'] = $cursor;
        if ($status !== null) $params['status'] = $status;

        $path = "/v1/broadcasts/{$id}/recipients";
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $response = $this->http->get($path);

        return [
            'data' => array_map(
                fn(array $item) => BroadcastRecipient::fromArray($item),
                $response['data'] ?? []
            ),
            'cursor' => $response['cursor'] ?? null,
        ];
    }

    /**
     * Send a broadcast immediately.
     *
     * @param string[] $includeTags Only send to contacts with ANY of these tags
     * @param string[] $excludeTags Exclude contacts with ANY of these tags
     */
    public function send(
        string $id,
        ?array $includeTags = null,
        ?array $excludeTags = null,
    ): Broadcast {
        $body = array_filter([
            'includeTags' => $includeTags,
            'excludeTags' => $excludeTags,
        ], fn($v) => $v !== null);

        $response = $this->http->post("/v1/broadcasts/{$id}/send", $body ?: null);
        return Broadcast::fromArray($response);
    }

    /**
     * Schedule a broadcast for later.
     *
     * @param string[] $includeTags Only send to contacts with ANY of these tags
     * @param string[] $excludeTags Exclude contacts with ANY of these tags
     */
    public function schedule(
        string $id,
        string $scheduledAt,
        ?array $includeTags = null,
        ?array $excludeTags = null,
    ): Broadcast {
        $body = array_filter([
            'scheduledAt' => $scheduledAt,
            'includeTags' => $includeTags,
            'excludeTags' => $excludeTags,
        ], fn($v) => $v !== null);

        $response = $this->http->post("/v1/broadcasts/{$id}/schedule", $body);
        return Broadcast::fromArray($response);
    }

    /**
     * Cancel a scheduled broadcast.
     */
    public function cancel(string $id): Broadcast
    {
        $response = $this->http->post("/v1/broadcasts/{$id}/cancel");
        return Broadcast::fromArray($response);
    }

    /**
     * Send a test email for a broadcast.
     *
     * @param string[] $to
     */
    public function test(string $id, array $to): TestBroadcastResponse
    {
        $response = $this->http->post("/v1/broadcasts/{$id}/test", ['to' => $to]);
        return TestBroadcastResponse::fromArray($response);
    }

    /**
     * Get analytics for a broadcast.
     */
    public function analytics(string $id): BroadcastAnalytics
    {
        $response = $this->http->get("/v1/broadcasts/{$id}/analytics");
        return BroadcastAnalytics::fromArray($response);
    }
}
