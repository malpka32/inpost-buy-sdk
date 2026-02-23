<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Category;

/**
 * Category – flat representation matching API response.
 * API returns: id, name, parentId, parent_id (flat list under "categories").
 */
final class CategoryDto
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $parentId = null,
    ) {
    }
}
