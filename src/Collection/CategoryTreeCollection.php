<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Category\CategoryTreeNode;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of category tree roots (each node has nested children).
 *
 * @extends AbstractCollection<CategoryTreeNode>
 */
final class CategoryTreeCollection extends AbstractCollection
{
    public function getType(): string
    {
        return \malpka32\InPostBuySdk\Dto\Category\CategoryTreeNode::class;
    }

    /**
     * @param CategoryTreeNode ...$nodes
     */
    public static function fromTreeNodes(CategoryTreeNode ...$nodes): self
    {
        return new self($nodes);
    }

    /**
     * @param list<CategoryTreeNode> $nodes
     */
    public static function fromArray(array $nodes): self
    {
        return new self($nodes);
    }
}
