<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Builders;

interface Builder
{
    public function build(?array $parameters = []): mixed;
}
