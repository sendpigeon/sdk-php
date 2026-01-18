<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum BroadcastStatus: string
{
    case Draft = 'DRAFT';
    case Scheduled = 'SCHEDULED';
    case Sending = 'SENDING';
    case Sent = 'SENT';
    case Cancelled = 'CANCELLED';
    case Failed = 'FAILED';
}
