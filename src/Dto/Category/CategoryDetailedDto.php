<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Category;

/**
 * Szczegółowa kategoria (OpenAPI: CategoryDetailed).
 *
 * Zawiera relations, metadata, children.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Categories
 */
final class CategoryDetailedDto
{
    /**
     * @param list<array<string, mixed>>|null $relations
     * @param list<CategoryDetailedDto>|null   $children
     */
    public function __construct(
        public string $id,
        public bool $leaf,
        public string $name,
        public string $description,
        public bool $doesNotRequireGpsrInfo,
        /** @var list<array<string, mixed>>|null */
        public ?array $relations = null,
        /** @var array<string, mixed>|null */
        public ?array $metadata = null,
        /** @var list<CategoryDetailedDto>|null */
        public ?array $children = null,
    ) {
    }
}
