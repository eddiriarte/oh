<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Hydrators;

interface Hydrator
{
    public function build(?array $parameters = []): mixed;
}
