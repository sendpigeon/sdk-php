<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class TrackingDefaults
{
    public function __construct(
        public bool $trackingEnabled,
        public bool $privacyMode,
        public bool $webhookOnEveryOpen,
        public bool $webhookOnEveryClick,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            trackingEnabled: $data['trackingEnabled'],
            privacyMode: $data['privacyMode'],
            webhookOnEveryOpen: $data['webhookOnEveryOpen'],
            webhookOnEveryClick: $data['webhookOnEveryClick'],
        );
    }
}
