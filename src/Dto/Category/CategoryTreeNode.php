<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Category;

/**
 * Node of category tree – built in memory from flat CategoryCollection.
 * Root nodes have parentId === null; children are in $children.
 */
final class CategoryTreeNode
{
    /**
     * @param list<CategoryTreeNode> $children
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $parentId,
        public readonly array $children = [],
    ) {
    }
}
