<?php

namespace EddIriarte\Oh\Handlers;

use EddIriarte\Oh\Attributes\ListMemberType;
use EddIriarte\Oh\Enums\StringCase;
use EddIriarte\Oh\Manager;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;

final class ReflectedAttributeValueExtractor
{
    public function __construct(private Manager $manager)
    {
    }

    public function extract(ReflectionParameter|ReflectionProperty $reflection, ?array $parameter): mixed
    {
        if (is_null($parameter)) {
            return null;
        }

        $sourceKey = $this->getSourceKey($reflection->getName(), $parameter);

        if (!$this->hasSourceKey($parameter, $sourceKey) && $reflection->isDefaultValueAvailable()) {
            return $reflection->getDefaultValue();
        }

        if (!$this->hasSourceKey($parameter, $sourceKey) && $reflection->allowsNull()) {
            return null;
        }

        $sourceValue = $parameter[$sourceKey];
        $reflectionType = $reflection->getType();

        if (is_null($reflectionType)) {
            return $sourceValue;
        }

        if ($this->hasArrayableWithObjects($sourceValue, $reflection, $reflectionType)) {
            return $this->getArrayableWithObjects($sourceValue, $reflection, $reflectionType);
        }

        if ($this->hasCompatibleType($sourceValue, $reflectionType)) {
            return $sourceValue;
        }

        if ($reflectionType instanceof ReflectionNamedType && !$reflectionType->isBuiltin()) {
            return $this->instantiateNestedObject($sourceValue, $reflectionType);
        }

        return null;
    }

    private function getSourceKey(string $reflectionName, array $sourceData): ?string
    {
        $caseType = $this->manager->getConfig('source_naming_case');
        if (is_string($caseType)) {
            $caseType = StringCase::from($caseType);
        }

        $key = $caseType->transform($reflectionName);

        if (is_array($key)) {
            $matches = array_filter(
                $key,
                fn (string $k) => array_key_exists($k, $sourceData)
            );
            $key = array_values($matches)[0] ?? null;
        }

        return $key;
    }

    private function hasSourceKey(array $values, ?string $sourceKey): bool
    {
        return !is_null($sourceKey) && array_key_exists($sourceKey, $values);
    }

    private function hasCompatibleType(mixed $sourceValue, ReflectionUnionType|ReflectionNamedType $reflectionType): bool
    {
        if ($reflectionType instanceof ReflectionUnionType) {
            $compatibleTypes = array_filter(
                $reflectionType->getTypes(),
                fn (ReflectionNamedType $type) => $this->hasCompatibleNamedType($type, $sourceValue)
            );

            return count($compatibleTypes) > 0;
        }

        return $this->hasCompatibleNamedType($reflectionType, $sourceValue);
    }

    private function hasCompatibleNamedType(ReflectionNamedType $reflectionType, mixed $sourceValue): bool
    {
        $reflectionTypeName = $reflectionType->getName();

        if (!$reflectionType->isBuiltin()) {
            return is_a($sourceValue, $reflectionTypeName);
        }

        return match (gettype($sourceValue)) {
            'boolean' => $reflectionTypeName === 'bool',
            'integer', 'double' => in_array($reflectionTypeName, ['float', 'double', 'int'], false),
            'string' => $reflectionTypeName === 'string',
             'array' => $reflectionTypeName === 'array',
            'object' => $reflectionTypeName === 'object',
            'resource' => $reflectionTypeName === 'resource',
            'resource (closed)' => $reflectionTypeName === 'resource',
            'NULL' => empty($reflectionTypeName),
            'unknown type' => empty($reflectionTypeName),
        };
    }

    private function instantiateNestedObject(mixed $sourceValue, ReflectionNamedType $reflectionType): mixed
    {
        return $this->manager->hydrate($reflectionType->getName(), $sourceValue);
    }

    private function hasArrayableWithObjects(
        mixed $sourceValue,
        ReflectionParameter|ReflectionProperty $reflection,
        ReflectionUnionType|ReflectionNamedType $reflectionType
    ): bool {
        if (gettype($sourceValue) !== 'array') {
            return false;
        }

        $attributes = $reflection->getAttributes(ListMemberType::class);
        if (count($attributes) < 1) {
            return false;
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            $arrayableTypes = array_filter(
                $reflectionType->getTypes(),
                fn (ReflectionNamedType $type) => $this->hasArrayableNamedType($type)
            );

            return count($arrayableTypes) > 0;
        }

        return $this->hasArrayableNamedType($reflectionType);
    }

    private function hasArrayableNamedType(ReflectionNamedType $type): bool
    {
        if ($type->isBuiltin()) {
            return $type->getName() === 'array';
        }

        $arrayClass = new ReflectionClass($type->getName());
        return $arrayClass->implementsInterface(\Traversable::class)
            || $arrayClass->implementsInterface(\ArrayAccess::class);
    }

    private function getArrayableWithObjects(
        array $sourceValue,
        ReflectionParameter|ReflectionProperty $reflection,
        ReflectionIntersectionType|ReflectionUnionType|ReflectionNamedType $reflectionType
    ): mixed {
        /** @var ListMemberType $attribute */
        $attribute = $reflection->getAttributes(ListMemberType::class)[0]->newInstance();

        $list = [];
        foreach ($sourceValue as $key => $member) {
            $list[$key] = $this->manager->hydrate($attribute->type, $member);
        }

        if ($reflectionType->getName() === 'array') {
            return $list;
        }

        return (new ReflectionClass($reflectionType->getName()))->newInstance($list);
    }
}
