<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class SendBatchResponse
{
    /**
     * @param BatchEmailResult[] $data
     */
    public function __construct(
        public array $data,
        public array $summary,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(
                fn(array $item) => BatchEmailResult::fromArray($item),
                $data['data']
            ),
            summary: $data['summary'],
        );
    }
}
