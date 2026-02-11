<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Collection;

use malpka32\InPostBuySdk\Dto\AttributeValueDto;
use Ramsey\Collection\AbstractCollection;

/**
 * Collection of attribute values.
 *
 * @extends AbstractCollection<AttributeValueDto>
 */
final class AttributeValueCollection extends AbstractCollection
{
    public function getType(): string
    {
        return AttributeValueDto::class;
    }

    /**
     * @param AttributeValueDto ...$items
     */
    public static function fromAttributes(AttributeValueDto ...$items): self
    {
        return new self($items);
    }

    /**
     * @param list<AttributeValueDto> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }
}
