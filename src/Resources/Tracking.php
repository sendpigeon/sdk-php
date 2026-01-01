<?php

declare(strict_types=1);

namespace SendPigeon\Resources;

use SendPigeon\HttpClient;
use SendPigeon\Types\TrackingDefaults;

class Tracking
{
    public function __construct(
        private readonly HttpClient $http,
    ) {}

    /**
     * Get organization tracking defaults.
     */
    public function getDefaults(): TrackingDefaults
    {
        $response = $this->http->get('/v1/tracking/defaults');
        return TrackingDefaults::fromArray($response);
    }

    /**
     * Update organization tracking defaults.
     *
     * @param bool|null $trackingEnabled Master toggle for tracking
     * @param bool|null $privacyMode Don't store IP addresses or user agents
     * @param bool|null $webhookOnEveryOpen Send webhook for every open
     * @param bool|null $webhookOnEveryClick Send webhook for every click
     */
    public function updateDefaults(
        ?bool $trackingEnabled = null,
        ?bool $privacyMode = null,
        ?bool $webhookOnEveryOpen = null,
        ?bool $webhookOnEveryClick = null,
    ): TrackingDefaults {
        $body = array_filter([
            'trackingEnabled' => $trackingEnabled,
            'privacyMode' => $privacyMode,
            'webhookOnEveryOpen' => $webhookOnEveryOpen,
            'webhookOnEveryClick' => $webhookOnEveryClick,
        ], fn($v) => $v !== null);

        $response = $this->http->patch('/v1/tracking/defaults', $body);
        return TrackingDefaults::fromArray($response);
    }
}
