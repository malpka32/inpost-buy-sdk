<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Deposit;

use malpka32\InPostBuySdk\Collection\DepositPositionCollection;
use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositPositionDto;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

final class OfferDepositPositionCollectionMapper
{
    public function __construct(
        /** @var ItemMapperInterface<DepositPositionDto> */
        private readonly ItemMapperInterface $depositPositionMapper = new OfferDepositPositionDtoMapper(),
    ) {
    }

    /**
     * @param mixed $depositsRaw
     * @return DepositPositionCollection|null
     */
    public function map(mixed $depositsRaw): ?DepositPositionCollection
    {
        if (!is_array($depositsRaw)) {
            return null;
        }

        $result = new DepositPositionCollection();
        foreach ($depositsRaw as $item) {
            if (!is_array($item)) {
                continue;
            }
            /** @var array<string, mixed> $item */
            if (!$this->depositPositionMapper->canProcess($item)) {
                continue;
            }
            $result->add($this->depositPositionMapper->mapItem($item));
        }

        return $result->isEmpty() ? null : $result;
    }
}
