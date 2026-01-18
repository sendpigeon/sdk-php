<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum ContactStatus: string
{
    case Active = 'ACTIVE';
    case Unsubscribed = 'UNSUBSCRIBED';
    case Bounced = 'BOUNCED';
    case Complained = 'COMPLAINED';
}
