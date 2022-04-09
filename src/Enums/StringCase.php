<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Enums;

use EddIriarte\Oh\Helpers\CaseMapper;

enum StringCase : string
{
    case SnakeCase = 'SNAKE_CASE';
    case KebabCase = 'KEBAB_CASE';
    case CamelCase = 'CAMEL_CASE';
    case StudlyCase = 'STUDLY_CASE';
    case AnyCase = 'ANY_CASE';

    public function transform(string $value): string|array|null
    {
        return match ($this) {
            StringCase::SnakeCase => CaseMapper::toSnakeCase($value),
            StringCase::KebabCase => CaseMapper::toKebabCase($value),
            StringCase::CamelCase => CaseMapper::toCamelCase($value),
            StringCase::StudlyCase => CaseMapper::toStudlyCase($value),
            StringCase::AnyCase => CaseMapper::toAllCases($value),
        };
    }
}
