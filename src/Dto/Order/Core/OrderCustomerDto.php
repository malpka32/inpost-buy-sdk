<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Dane kupującego.
 */
final class OrderCustomerDto
{
    public function __construct(
        public ?string $email = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phoneNumber = null,
        public ?OrderAddressDto $address = null,
    ) {
    }
}
