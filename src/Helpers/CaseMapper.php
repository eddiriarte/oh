<?php

declare(strict_types=1);

namespace EddIriarte\Oh\Helpers;

class CaseMapper
{
    private static array $snakeCache = [];
    private static array $camelCache = [];
    private static array $studlyCache = [];

    public static function toCamelCase(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::toStudlyCase($value));
    }

    public static function toStudlyCase(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $words = explode(' ', str_replace(['-', '_'], ' ', $value));

        $studlyWords = array_map(function ($word) {
            return ucfirst($word);
        }, $words);

        return static::$studlyCache[$key] = implode($studlyWords);
    }

    public static function toSnakeCase(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    public static function toKebabCase(string $value): string
    {
        return static::toSnakeCase($value, '-');
    }

    public static function toAllCases(string $value): array
    {
        return [
            static::toSnakeCase($value),
            static::toCamelCase($value),
            static::toStudlyCase($value),
            static::toKebabCase($value),
        ];
    }
}
