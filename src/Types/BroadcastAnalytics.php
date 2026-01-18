<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class BroadcastAnalytics
{
    public function __construct(
        public array $opensOverTime,
        public array $linkPerformance,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            opensOverTime: $data['opensOverTime'] ?? [],
            linkPerformance: $data['linkPerformance'] ?? [],
        );
    }
}
