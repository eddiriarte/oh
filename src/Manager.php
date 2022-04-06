<?php

declare(strict_types=1);

namespace EddIriarte\Oh;

use EddIriarte\Oh\Builders\Builder;
use EddIriarte\Oh\Builders\ConstructedInstanceBuilder;
use EddIriarte\Oh\Builders\PlainInstanceBuilder;
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

    private function initBuilder(string $className): Builder
    {
        $reflectionClass = new ReflectionClass($className);

        if ($reflectionClass->hasMethod('__construct')
            && !empty($reflectionClass->getMethod('__construct')->getParameters())) {
            return new ConstructedInstanceBuilder($className, $this);
        }

        return new PlainInstanceBuilder($className, $this);
    }
}
