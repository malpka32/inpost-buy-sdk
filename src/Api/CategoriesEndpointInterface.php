<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

/**
 * Categories endpoint contract – enables testing with fake data.
 */
interface CategoriesEndpointInterface
{
    /**
     * Browse category tree.
     *
     * @param string|null $categoryId Category ID (subtree root)
     * @param int|null    $depth      Subtree depth 0–4
     * @return array<string, mixed>|list<array<string, mixed>>
     */
    public function fetch(?string $categoryId = null, ?int $depth = null): array;

    /**
     * Get category details.
     *
     * @return array<string, mixed>
     */
    public function get(string $categoryId, ?int $depth = null): array;

    /**
     * Get category attributes.
     *
     * @return list<array<string, mixed>>
     */
    public function getAttributes(string $categoryId): array;
}
