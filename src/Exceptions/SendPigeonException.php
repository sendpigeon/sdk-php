<?php

declare(strict_types=1);

namespace SendPigeon\Exceptions;

use Exception;

class SendPigeonException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly ?string $apiCode = null,
        public readonly ?int $status = null,
    ) {
        parent::__construct($message);
    }

    public static function network(string $message): self
    {
        return new self($message, 'network_error');
    }

    public static function timeout(string $message): self
    {
        return new self($message, 'timeout_error');
    }

    public static function api(int $status, ?string $apiCode, string $message): self
    {
        return new self($message, 'api_error', $apiCode, $status);
    }
}
