<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Hydrators;

use EddIriarte\Oh\Handlers\ReflectedAttributeValueExtractor;
use ReflectionClass;

final class InstanceByPropertiesHydrator extends BaseHydrator
{
    public function build(?array $parameters = []): mixed
    {
        $extractor = new ReflectedAttributeValueExtractor($this->getManager());
        $classProperties = (new ReflectionClass($this->getTargetClass()))
            ->getProperties($this->propertyVisibility());

        $instance = (new ReflectionClass($this->getTargetClass()))->newInstance();
        foreach ($classProperties as $reflectionProperty) {
            $reflectionProperty->setValue($instance, $extractor->extract($reflectionProperty, $parameters));
        }

        return $instance;
    }
}
