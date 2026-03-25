<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Line;

use malpka32\InPostBuySdk\Collection\OrderLineCollection;
use malpka32\InPostBuySdk\Dto\Order\Line\OrderLineDto;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<OrderLineCollection>
 */
final class OrderLineCollectionMapper implements CollectionMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<OrderLineDto> */
        private readonly ItemMapperInterface $lineMapper = new OrderLineDtoMapper(),
    ) {
    }

    /**
     * @param list<array<string, mixed>>|array<string, mixed> $data List of raw order line payloads
     */
    public function map(array $data): OrderLineCollection
    {
        $collection = new OrderLineCollection();
        if ($data === [] || !array_is_list($data)) {
            return $collection;
        }
        foreach ($data as $line) {
            if (!is_array($line)) {
                continue;
            }
            /** @var array<string, mixed> $line */
            if (!$this->lineMapper->canProcess($line)) {
                continue;
            }
            $collection->add($this->lineMapper->mapItem($line));
        }

        return $collection;
    }
}
