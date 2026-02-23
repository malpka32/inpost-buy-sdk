<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Attribute;

use malpka32\InPostBuySdk\Collection\AttributeValueCollection;
use malpka32\InPostBuySdk\Dto\Attribute\AttributeValueDto;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<AttributeValueCollection>
 */
final class AttributeValueCollectionMapper implements CollectionMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<AttributeValueDto> */
        private readonly ItemMapperInterface $itemMapper = new AttributeValueDtoMapper(),
    ) {
    }

    /**
     * @param list<array<string, mixed>> $data
     */
    public function map(array $data): AttributeValueCollection
    {
        $collection = new AttributeValueCollection();
        if ($data === []) {
            return $collection;
        }
        foreach ($data as $item) {
            /** @var array<string, mixed> $item */
            if (!$this->itemMapper->canProcess($item)) {
                continue;
            }
            $collection->add($this->itemMapper->mapItem($item));
        }
        return $collection;
    }
}
