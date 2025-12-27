<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum ApiKeyPermission: string
{
    case FullAccess = 'full_access';
    case Sending = 'sending';
}
