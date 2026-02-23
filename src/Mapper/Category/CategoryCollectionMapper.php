<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Category;

use malpka32\InPostBuySdk\Collection\CategoryCollection;
use malpka32\InPostBuySdk\Dto\Category\CategoryDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<CategoryCollection>
 * @implements ItemMapperInterface<CategoryDto>
 */
final class CategoryCollectionMapper implements CollectionMapperInterface, ItemMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<CategoryDto> */
        private readonly ItemMapperInterface $itemMapper = new CategoryDtoMapper(),
    ) {
    }

    public function map(array $data): CategoryCollection
    {
        $collection = new CategoryCollection();
        $list = ArrayHelper::getList($data, ['categories', 'items']);
        foreach ($list as $item) {
            if (!$this->canProcess($item)) {
                continue;
            }
            $collection->add($this->mapItem($item));
        }
        return $collection;
    }

    public function canProcess(array $item): bool
    {
        return $this->itemMapper->canProcess($item);
    }

    public function mapItem(mixed $item): CategoryDto
    {
        return $this->itemMapper->mapItem($item);
    }
}
