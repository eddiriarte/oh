<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Hydrators;

use EddIriarte\Oh\Enums\PropertyVisibility;
use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use ReflectionNamedType;
use ReflectionUnionType;
use Symfony\Component\Console\Helper\Dumper;

abstract class BaseHydrator implements Hydrator
{
    public function __construct(
        private string $className,
        private Manager $manager
    ) {
    }

    protected function getTargetClass(): string
    {
        return $this->className;
    }

    protected function getManager(): Manager
    {
        return $this->manager;
    }

    public function propertyVisibility(): PropertyVisibility
    {
        return $this->manager->getConfig('property_visibility');
    }
}
