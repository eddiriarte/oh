<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Builders;

use ReflectionClass;
use ReflectionNamedType;

class ConstructedInstanceBuilder extends BaseBuilder
{
    public function build(?array $parameters = []): mixed
    {
        $constructorParameters = (new ReflectionClass($this->className()))
            ->getMethod('__construct')
            ->getParameters();

        $parameterList = [];

        foreach ($constructorParameters as $reflectionParameter) {
            $param = $reflectionParameter->getName();
            $sourceKey = $this->getSourceKey($param, $parameters);

            if (!$this->hasParameterKey($parameters, $sourceKey) && $reflectionParameter->isDefaultValueAvailable()) {
                $parameterList[] = $reflectionParameter->getDefaultValue();
                continue;
            }

            if (!$this->hasParameterKey($parameters, $sourceKey) && $reflectionParameter->allowsNull()) {
                $parameterList[$param] = null;
                continue;
            }

            $sourceValue = $parameters[$sourceKey];
            $propertyType = $reflectionParameter->getType();

            if ($this->hasCompatibleType($sourceValue, $propertyType)) {
                $parameterList[] = $sourceValue;
                continue;
            }

            if ($propertyType instanceof ReflectionNamedType && !$propertyType->isBuiltin()) {
                $instanceValue = $this->instantiateNestedObject($sourceValue, $propertyType);
                $parameterList[] = $instanceValue;
                continue;
            }
        }

        return (new ReflectionClass($this->className()))->newInstance(...$parameterList);
    }
}
