<?php

declare(strict_types=1);

namespace Tests\Oh\Samples;

class NestedHeroDto
{
    public ?PublicPersonDto $alias = null;
    public ?float $strength = 100;
    public bool $flying = false;
    public ?string $psy = null;
    public string $name;
}
