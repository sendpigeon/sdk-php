<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class TrackingOptions
{
    public function __construct(
        public ?bool $opens = null,
        public ?bool $clicks = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'opens' => $this->opens,
            'clicks' => $this->clicks,
        ], fn($v) => $v !== null);
    }
}
