<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum SuppressionReason: string
{
    case HardBounce = 'hard_bounce';
    case Complaint = 'complaint';
}
