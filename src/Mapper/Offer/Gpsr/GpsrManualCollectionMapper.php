<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Gpsr;

use malpka32\InPostBuySdk\Collection\GpsrManualCollection;
use malpka32\InPostBuySdk\Dto\Offer\Gpsr\GpsrManualDto;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<GpsrManualCollection>
 */
final class GpsrManualCollectionMapper implements CollectionMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<GpsrManualDto> */
        private readonly ItemMapperInterface $itemMapper = new GpsrManualDtoMapper(),
    ) {
    }

    /**
     * @param list<array<string, mixed>> $data Raw manuals array from API
     */
    public function map(array $data): GpsrManualCollection
    {
        $collection = new GpsrManualCollection();
        if ($data === []) {
            return $collection;
        }
        foreach ($data as $item) {
            if (!$this->itemMapper->canProcess($item)) {
                continue;
            }
            $collection->add($this->itemMapper->mapItem($item));
        }
        return $collection;
    }
}
