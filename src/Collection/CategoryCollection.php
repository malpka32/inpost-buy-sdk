<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\CategoryDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of categories.
 *
 * @extends AbstractCollection<CategoryDto>
 */
final class CategoryCollection extends AbstractCollection
{
    public function getType(): string
    {
        return CategoryDto::class;
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
