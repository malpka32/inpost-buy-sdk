<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order;

use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Dto\Order\OrderDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<OrderCollection>
 * @implements ItemMapperInterface<OrderDto>
 */
final class OrderCollectionMapper implements CollectionMapperInterface, ItemMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<OrderDto> */
        private readonly ItemMapperInterface $itemMapper = new OrderDtoMapper(),
    ) {
    }

    public function map(array $data): OrderCollection
    {
        $collection = new OrderCollection();
        $list = ArrayHelper::getList($data, ['items', 'orders']);
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

    public function mapItem(mixed $item): OrderDto
    {
        return $this->itemMapper->mapItem($item);
    }
}
