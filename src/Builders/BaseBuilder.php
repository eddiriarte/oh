<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Builders;

use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use ReflectionNamedType;
use ReflectionUnionType;

abstract class BaseBuilder implements Builder
{
    public function __construct(
        private string $className,
        private Manager $manager
    ) {
    }

    protected function className(): string
    {
        return $this->className;
    }

    protected function getManager(): Manager
    {
        return $this->manager;
    }

    public function propertyVisibility(): int
    {
        return $this->manager->getConfig('property_visibility');
    }

    protected function sourceNamingCase(): StringCase
    {
        $caseType = $this->manager->getConfig('source_naming_case');
        if (is_string($caseType)) {
            $caseType = StringCase::from($caseType);
        }

        return $caseType;
    }

    protected function getSourceKey(string $propertyName, array $sourceData): ?string
    {
        $key = $this->sourceNamingCase()->map($propertyName);

        if (is_array($key)) {
            $matches = array_filter(
                $key,
                fn (string $k) => array_key_exists($k, $sourceData)
            );
            $key = array_values($matches)[0] ?? null;
        }

        return $key;
    }

    protected function hasParameterKey(array $parameters, ?string $sourceKey): bool
    {
        return !is_null($sourceKey) && array_key_exists($sourceKey, $parameters);
    }

    protected function hasCompatibleType(
        mixed $sourceValue,
        ReflectionUnionType|ReflectionNamedType|null $propertyType
    ): bool {
        if (is_null($propertyType)) {
            return true;
        }

        if ($propertyType instanceof ReflectionUnionType) {
            $compatibleTypes = array_filter(
                $propertyType->getTypes(),
                fn (ReflectionNamedType $type) => $this->hasCompatibleNamedType($type, $sourceValue)
            );

            return count($compatibleTypes) > 0;
        }

        return $this->hasCompatibleNamedType($propertyType, $sourceValue);
    }

    protected function hasCompatibleNamedType(
        ReflectionNamedType $propertyType,
        mixed $sourceValue
    ): bool {
        if ($propertyType->isBuiltin()) {
            return match (gettype($sourceValue)) {
                'boolean' => $propertyType->getName() === 'bool',
                'integer' => $propertyType->getName() === 'int',
                'double' => $propertyType->getName() === 'float',
                'string' => $propertyType->getName() === 'string',
                'array' => $propertyType->getName() === 'array',
                'object' => $propertyType->getName() === 'object',
                'resource' => $propertyType->getName() === 'resource',
                'resource (closed)' => $propertyType->getName() === 'resource',
                'NULL' => empty($propertyType->getName()),
                'unknown type' => empty($propertyType->getName()),
            };
        }

        return is_a($sourceValue, $propertyType->getName());
    }

    protected function instantiateNestedObject(mixed $sourceValue, ReflectionNamedType $propertyType): mixed
    {
        return $this->manager->build($propertyType->getName(), $sourceValue);
    }
}
