<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

/**
 * Helper for safe array access (API responses).
 * PSR-4: class in Mapper namespace.
 */
final class ArrayHelper
{
    /**
     * Gets value from array, checking keys in order.
     *
     * @param array<string, mixed> $data
     * @param string|list<string>  $keys    Key or list of keys (first match wins)
     * @param mixed                $default Default value
     */
    public static function get(array $data, string|array $keys, mixed $default = null): mixed
    {
        $keys = is_array($keys) ? $keys : [$keys];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                return $data[$key];
            }
        }
        return $default;
    }

    /**
     * Gets list from array (e.g. items, orders), checking keys in order.
     *
     * @param array<string, mixed> $data
     * @param list<string>         $keys    Keys to check (items, orders, …)
     * @return list<array<string, mixed>>
     */
    public static function getList(array $data, array $keys = ['items', 'categories', 'orders']): array
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $filtered = array_values(array_filter($data[$key], 'is_array'));
                /** @var list<array<string, mixed>> $filtered */
                return $filtered;
            }
        }
        if (isset($data['id'])) {
            return [$data];
        }
        return [];
    }

    /**
     * Extracts offer object from OfferDetails (metadata + offer structure) or returns item if already an offer.
     *
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    public static function extractOffer(array $item): array
    {
        $offer = $item['offer'] ?? $item;
        if (!is_array($offer)) {
            return [];
        }
        /** @var array<string, mixed> $offer */
        return $offer;
    }

    /**
     * Safe string from mixed (level 10: no direct cast of mixed to string).
     */
    public static function asString(mixed $value): string
    {
        return is_string($value) ? $value : (is_scalar($value) ? (string) $value : '');
    }

    /**
     * Safe int from mixed.
     */
    public static function asInt(mixed $value): int
    {
        return is_int($value) ? $value : (is_scalar($value) ? (int) $value : 0);
    }

    /**
     * Safe float from mixed.
     */
    public static function asFloat(mixed $value): float
    {
        return is_float($value) ? $value : (is_scalar($value) ? (float) $value : 0.0);
    }
}
