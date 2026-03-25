<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderMoneyDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderMoneyDto>
 */
final class OrderMoneyDtoMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?OrderMoneyDto
    {
        if (!is_array($data)) {
            return null;
        }
        if (!array_key_exists('amount', $data) || !array_key_exists('currency', $data)) {
            return null;
        }
        $currency = ArrayHelper::asString($data['currency']);
        if ($currency === '') {
            return null;
        }

        return new OrderMoneyDto(
            amount: ArrayHelper::asFloat($data['amount']),
            currency: $currency,
        );
    }
}
