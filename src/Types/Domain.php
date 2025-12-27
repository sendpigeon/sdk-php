<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class Domain
{
    public function __construct(
        public string $id,
        public string $name,
        public DomainStatus $status,
        public string $createdAt,
        public ?string $verifiedAt = null,
        public ?string $lastCheckedAt = null,
        public ?string $failingSince = null,
        public array $dnsRecords = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            status: DomainStatus::from($data['status']),
            createdAt: $data['created_at'],
            verifiedAt: $data['verified_at'] ?? null,
            lastCheckedAt: $data['last_checked_at'] ?? null,
            failingSince: $data['failing_since'] ?? null,
            dnsRecords: array_map(
                fn(array $r) => DnsRecord::fromArray($r),
                $data['dns_records'] ?? []
            ),
        );
    }
}
