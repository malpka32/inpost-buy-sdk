<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\Deposit;

use malpka32\InPostBuySdk\Dto\Offer\Deposit\DepositTypeDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;

/**
 * @implements ItemMapperInterface<DepositTypeDto>
 */
final class DepositTypeMapper implements ItemMapperInterface
{
    public function canProcess(array $item): bool
    {
        return true;
    }

    public function mapItem(mixed $item): DepositTypeDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $priceRaw = $item['price'] ?? null;
        $price = is_array($priceRaw) ? $priceRaw : [];

        return new DepositTypeDto(
            id: ArrayHelper::asString(ArrayHelper::get($item, 'id') ?? ''),
            amount: ArrayHelper::asFloat(ArrayHelper::get($price, 'amount') ?? 0.0),
            currency: ArrayHelper::asString(ArrayHelper::get($price, 'currency') ?? 'PLN'),
        );
    }
}
