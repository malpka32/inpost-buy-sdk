<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Helper;

/**
 * Helper for safe array access (API responses).
 */
final class ArrayHelper
{
    /**
     * Gets value from array, checking keys in order.
     *
     * @param array<string, mixed> $data   JSON object (string keys)
     * @param string|list<string>  $keys   Key or list of keys (first match wins)
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
     * Gets list from array (e.g. items, orders), or when response is already a list.
     *
     * @param array<string, mixed>|list<mixed> $data
     * @param list<string>                     $keys Keys to check (items, orders, ...)
     * @return list<array<string, mixed>>
     */
    public static function getList(array $data, array $keys = ['items', 'categories', 'orders']): array
    {
        if (array_is_list($data)) {
            $filtered = array_values(array_filter($data, 'is_array'));
            /** @var list<array<string, mixed>> $filtered */
            return $filtered;
        }

        foreach ($keys as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $filtered = array_values(array_filter($data[$key], 'is_array'));
                /** @var list<array<string, mixed>> $filtered */
                return $filtered;
            }
        }

        if (isset($data['id'])) {
            /** @var list<array<string, mixed>> $single */
            $single = [$data];
            return $single;
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

    public static function asString(mixed $value): string
    {
        return is_string($value) ? $value : (is_scalar($value) ? (string) $value : '');
    }

    public static function asInt(mixed $value): int
    {
        return is_int($value) ? $value : (is_scalar($value) ? (int) $value : 0);
    }

    public static function asFloat(mixed $value): float
    {
        return is_float($value) ? $value : (is_scalar($value) ? (float) $value : 0.0);
    }

    public static function asBoolOrNull(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }
        return is_bool($value) ? $value : (bool) $value;
    }
}
