<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderAddressDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderCustomerDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\SingleItemMapperInterface;

/**
 * @implements SingleItemMapperInterface<OrderCustomerDto>
 */
final class OrderCustomerDtoMapper implements SingleItemMapperInterface
{
    public function __construct(
        private readonly OrderAddressDtoMapper $addressMapper = new OrderAddressDtoMapper(),
    ) {
    }

    public function map(mixed $data): ?OrderCustomerDto
    {
        if (!is_array($data)) {
            return null;
        }
        /** @var array<string, mixed> $data */

        $email = ArrayHelper::get($data, 'email');
        $firstName = ArrayHelper::get($data, 'firstName');
        $lastName = ArrayHelper::get($data, 'lastName');
        $phoneNumber = ArrayHelper::get($data, 'phoneNumber');
        $address = $this->addressMapper->map(ArrayHelper::get($data, 'address'));

        return new OrderCustomerDto(
            email: $email === null ? null : ArrayHelper::asString($email),
            firstName: $firstName === null ? null : ArrayHelper::asString($firstName),
            lastName: $lastName === null ? null : ArrayHelper::asString($lastName),
            phoneNumber: $phoneNumber === null ? null : ArrayHelper::asString($phoneNumber),
            address: $address instanceof OrderAddressDto ? $address : null,
        );
    }
}
