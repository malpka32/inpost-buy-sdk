<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper;

use malpka32\InPostBuySdk\Collection\CategoryCollection;
use malpka32\InPostBuySdk\Dto\CategoryDto;

/**
 * Maps API response to CategoryCollection.
 */
final class CategoryResponseMapper implements ResponseMapperInterface
{
    public function map(array $data): CategoryCollection
    {
        $collection = new CategoryCollection();
        $list = ArrayHelper::getList($data, ['items', 'categories']);
        foreach ($list as $item) {
            $collection->add(new CategoryDto(
                id: ArrayHelper::get($item, 'id'),
                name: ArrayHelper::get($item, 'name'),
                parentId: ArrayHelper::get($item, ['parent_id', 'parentId']),
            ));
        }
        return $collection;
    }
}
