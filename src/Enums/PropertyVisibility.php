<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Enums;

use ReflectionProperty;

enum PropertyVisibility : string
{
    case Public = 'PUBLIC';
    case Private = 'PRIVATE';
    case ReadOnly = 'READ_ONLY';
    case Protected = 'PROTECTED';

    public function reflectionPropertyVisibility(): int
    {
        return match ($this) {
            PropertyVisibility::Public => ReflectionProperty::IS_PUBLIC,
            PropertyVisibility::Private => ReflectionProperty::IS_PRIVATE,
            PropertyVisibility::ReadOnly => ReflectionProperty::IS_READONLY,
            PropertyVisibility::Protected => ReflectionProperty::IS_PROTECTED,
        };
    }
}
