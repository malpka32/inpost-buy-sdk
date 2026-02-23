<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\CollectionMapperInterface;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements CollectionMapperInterface<OfferCollection>
 * @implements ItemMapperInterface<OfferDto>
 */
final class OfferCollectionMapper implements CollectionMapperInterface, ItemMapperInterface
{
    public function __construct(
        /** @var ItemMapperInterface<OfferDto> */
        private readonly ItemMapperInterface $itemMapper = new OfferDtoMapper(),
    ) {
    }

    public function map(array $data): OfferCollection
    {
        $collection = new OfferCollection();
        $list = ArrayHelper::getList($data, ['data', 'items', 'offers']);
        foreach ($list as $item) {
            if ($this->canProcess($item)) {
                $collection->add($this->mapItem(ArrayHelper::extractOffer($item)));
            }
        }
        return $collection;
    }

    public function canProcess(array $item): bool
    {
        $offer = ArrayHelper::extractOffer($item);
        if (!is_array($offer)) {
            return false;
        }
        return $this->itemMapper->canProcess($offer);
    }

    public function mapItem(mixed $item): OfferDto
    {
        return $this->itemMapper->mapItem($item);
    }
}
