<?php

declare(strict_types=1);

namespace SendPigeon\Types;

enum ApiKeyMode: string
{
    case Live = 'live';
    case Test = 'test';
}
