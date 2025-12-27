<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class DomainVerificationResult
{
    public function __construct(
        public bool $verified,
        public DomainStatus $status,
        public array $dnsRecords = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            verified: $data['verified'],
            status: DomainStatus::from($data['status']),
            dnsRecords: array_map(
                fn(array $r) => DnsRecord::fromArray($r),
                $data['dns_records'] ?? []
            ),
        );
    }
}
