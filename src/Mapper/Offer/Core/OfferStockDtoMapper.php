<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Core;

use malpka32\InPostBuySdk\Dto\Offer\StockDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<StockDto>
 */
final class OfferStockDtoMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return true;
    }

    public function mapItem(mixed $item): StockDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        return new StockDto(
            quantity: ArrayHelper::asInt($item['quantity'] ?? 0),
            unit: ArrayHelper::asString(ArrayHelper::get($item, 'unit') ?? 'UNIT'),
        );
    }
}
