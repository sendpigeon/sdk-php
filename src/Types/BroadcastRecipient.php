<?php

declare(strict_types=1);

namespace SendPigeon\Types;

readonly class BroadcastRecipient
{
    public function __construct(
        public string $id,
        public string $contactId,
        public string $email,
        public BroadcastRecipientStatus $status,
        public ?string $sentAt = null,
        public ?string $deliveredAt = null,
        public ?string $openedAt = null,
        public ?string $clickedAt = null,
        public ?string $bouncedAt = null,
        public ?string $failedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            contactId: $data['contactId'],
            email: $data['email'],
            status: BroadcastRecipientStatus::from($data['status']),
            sentAt: $data['sentAt'] ?? null,
            deliveredAt: $data['deliveredAt'] ?? null,
            openedAt: $data['openedAt'] ?? null,
            clickedAt: $data['clickedAt'] ?? null,
            bouncedAt: $data['bouncedAt'] ?? null,
            failedAt: $data['failedAt'] ?? null,
        );
    }
}
