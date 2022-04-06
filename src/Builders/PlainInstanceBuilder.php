<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Builders;

use ReflectionClass;
use ReflectionNamedType;

class PlainInstanceBuilder extends BaseBuilder
{
    public function build(?array $parameters = []): mixed
    {
        $instance = (new ReflectionClass($this->className()))->newInstance();
        $classProperties = (new ReflectionClass($this->className()))
            ->getProperties($this->propertyVisibility());

        foreach ($classProperties as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            $sourceKey = $this->getSourceKey($property, $parameters);

            if (!$this->hasParameterKey($parameters, $sourceKey)) {
                continue;
            }

            $sourceValue = $parameters[$sourceKey];
            $propertyType = $reflectionProperty->getType();

            if ($this->hasCompatibleType($sourceValue, $propertyType)) {
                $reflectionProperty->setValue($instance, $sourceValue);
                continue;
            }

            if ($propertyType instanceof ReflectionNamedType && !$propertyType->isBuiltin()) {
                $instanceValue = $this->instantiateNestedObject($sourceValue, $propertyType);
                $reflectionProperty->setValue($instance, $instanceValue);
                continue;
            }
        }

        return $instance;
    }
}
