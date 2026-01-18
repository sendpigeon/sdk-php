<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum BroadcastRecipientStatus: string
{
    case Pending = 'PENDING';
    case Sent = 'SENT';
    case Delivered = 'DELIVERED';
    case Opened = 'OPENED';
    case Clicked = 'CLICKED';
    case Bounced = 'BOUNCED';
    case Complained = 'COMPLAINED';
    case Failed = 'FAILED';
}
