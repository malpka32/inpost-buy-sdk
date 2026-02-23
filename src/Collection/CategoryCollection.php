<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\Category\CategoryDto;
use malpka32\InPostBuySdk\Builder\CategoryTreeBuilder;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of categories (flat list from API).
 *
 * @extends AbstractCollection<CategoryDto>
 */
final class CategoryCollection extends AbstractCollection
{
    public function getType(): string
    {
        return \malpka32\InPostBuySdk\Dto\Category\CategoryDto::class;
    }

    /**
     * Builds category tree in memory from this flat list (no API call).
     */
    public function toTree(): CategoryTreeCollection
    {
        return (new CategoryTreeBuilder())->build($this);
    }

    /**
     * @param CategoryDto ...$categories
     */
    public static function fromCategories(CategoryDto ...$categories): self
    {
        return new self($categories);
    }

    /**
     * @param list<CategoryDto> $categories
     */
    public static function fromArray(array $categories): self
    {
        return new self($categories);
    }
}
