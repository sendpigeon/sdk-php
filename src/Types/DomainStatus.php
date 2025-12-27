<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum DomainStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case TemporaryFailure = 'temporary_failure';
    case Failed = 'failed';
}
