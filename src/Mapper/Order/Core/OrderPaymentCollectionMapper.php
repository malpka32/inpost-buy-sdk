<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Collection\OrderPaymentCollection;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderPaymentDto;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<OrderPaymentCollection>
 */
final class OrderPaymentCollectionMapper implements CollectionMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<OrderPaymentDto> */
        private readonly ItemMapperInterface $itemMapper = new OrderPaymentDtoMapper(),
    ) {
    }

    public function map(array $data): OrderPaymentCollection
    {
        $collection = new OrderPaymentCollection();
        if (!array_is_list($data)) {
            return $collection;
        }
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }
            /** @var array<string, mixed> $item */
            if (!$this->itemMapper->canProcess($item)) {
                continue;
            }
            $collection->add($this->itemMapper->mapItem($item));
        }

        return $collection;
    }
}
