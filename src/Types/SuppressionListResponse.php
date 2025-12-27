<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class SuppressionListResponse
{
    /**
     * @param Suppression[] $data
     */
    public function __construct(
        public array $data,
        public int $total,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(
                fn(array $item) => Suppression::fromArray($item),
                $data['data'] ?? []
            ),
            total: $data['total'],
        );
    }
}
