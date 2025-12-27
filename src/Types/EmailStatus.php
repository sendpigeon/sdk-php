<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum EmailStatus: string
{
    case Scheduled = 'scheduled';
    case Cancelled = 'cancelled';
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Bounced = 'bounced';
    case Complained = 'complained';
    case Failed = 'failed';
}
