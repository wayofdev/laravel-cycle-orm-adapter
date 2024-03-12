<?php

declare(strict_types=1);

namespace WayOfDev\Cycle\Support;

use function array_key_exists;
use function explode;
use function is_array;
use function is_null;
use function str_contains;

/**
 * Credits: most of this class methods and implementations
 * belongs to the Arr helper of laravel/framework project
 * (https://github.com/laravel/framework).
 *
 * @internal
 */
final class Arr
{
    public static function has(array $array, int|string $key): bool
    {
        $key = (string) $key;

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    public static function get(array $array, int|string $key, mixed $default = null): mixed
    {
        $key = (string) $key;

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     */
    public static function wrap(mixed $value): array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}
