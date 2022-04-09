<?php

declare(strict_types=1);

namespace EddIriarte\Oh;

use EddIriarte\Oh\Hydrators\Hydrator;
use EddIriarte\Oh\Hydrators\InstanceByConstructorHydrator;
use EddIriarte\Oh\Hydrators\InstanceByPropertiesHydrator;
use EddIriarte\Oh\Enums\StringCase;
use ReflectionClass;
use ReflectionProperty;

class Manager
{
    private array $config;
    private array $builders = [];

    public function __construct(array $config = [])
    {
        $this->config = $config + [
            'property_visibility' => ReflectionProperty::IS_PUBLIC,
            'source_naming_case' => StringCase::SnakeCase,
        ];
    }

    public function getConfig(string $option): mixed
    {
        return $this->config[$option] ?? null;
    }

    public function build(string $className, ?array $parameters = []): mixed
    {
        if (!isset($this->config[$className])) {
            $this->config[$className] = $this->initBuilder($className);
        }

        return $this->config[$className]->build($parameters);
    }

    private function initBuilder(string $className): Hydrator
    {
        $reflectionClass = new ReflectionClass($className);

        if ($reflectionClass->hasMethod('__construct')
            && !empty($reflectionClass->getMethod('__construct')->getParameters())) {
            return new InstanceByConstructorHydrator($className, $this);
        }

        return new InstanceByPropertiesHydrator($className, $this);
    }
}
