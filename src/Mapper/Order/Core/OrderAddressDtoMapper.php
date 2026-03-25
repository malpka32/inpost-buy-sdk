<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderAddressDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderAddressDto>
 */
final class OrderAddressDtoMapper implements SingleItemMapperInterface
{
    public function map(mixed $data): ?OrderAddressDto
    {
        if (!is_array($data)) {
            return null;
        }
        /** @var array<string, mixed> $data */

        $street = ArrayHelper::get($data, 'street');
        $city = ArrayHelper::get($data, 'city');
        $postCode = ArrayHelper::get($data, 'postCode');
        $state = ArrayHelper::get($data, 'state');
        $countryCode = ArrayHelper::get($data, 'countryCode');
        $building = ArrayHelper::get($data, 'building');
        $flat = ArrayHelper::get($data, 'flat');

        return new OrderAddressDto(
            street: $street === null ? null : ArrayHelper::asString($street),
            city: $city === null ? null : ArrayHelper::asString($city),
            postCode: $postCode === null ? null : ArrayHelper::asString($postCode),
            state: $state === null ? null : ArrayHelper::asString($state),
            countryCode: $countryCode === null ? null : ArrayHelper::asString($countryCode),
            building: $building === null ? null : ArrayHelper::asString($building),
            flat: $flat === null ? null : ArrayHelper::asString($flat),
        );
    }
}
