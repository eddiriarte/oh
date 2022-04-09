<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Hydrators;

use EddIriarte\Oh\Handlers\ReflectedAttributeValueExtractor;
use ReflectionClass;

final class InstanceByConstructorHydrator extends BaseHydrator
{
    public function build(?array $parameters = []): mixed
    {
        $extractor = new ReflectedAttributeValueExtractor($this->getManager());
        $constructorParameters = (new ReflectionClass($this->getTargetClass()))
            ->getMethod('__construct')
            ->getParameters();

        $parameterList = [];
        foreach ($constructorParameters as $reflectionParameter) {
            $parameterList[$reflectionParameter->getName()] = $extractor->extract($reflectionParameter, $parameters);
        }

        return (new ReflectionClass($this->getTargetClass()))->newInstance(...$parameterList);
    }
}
