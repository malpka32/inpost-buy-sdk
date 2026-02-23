<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Offer\PostSale;

use malpka32\InPostBuySdk\Dto\Offer\ShippingTimeDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<ShippingTimeDto>
 */
final class OfferShippingTimeSingleMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?ShippingTimeDto
    {
        if (!is_array($data)) {
            return null;
        }
        if (!array_key_exists('daysToShip', $data)) {
            return null;
        }

        return new ShippingTimeDto(
            daysToShip: ArrayHelper::asInt($data['daysToShip'] ?? 0),
        );
    }
}
