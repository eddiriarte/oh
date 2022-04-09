<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

class ReadOnlyPersonObject
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName
    ) {
    }
}
