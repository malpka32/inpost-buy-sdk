<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Address used in customer, invoice and delivery data.
 */
final class OrderAddressDto
{
    public function __construct(
        public ?string $street = null,
        public ?string $city = null,
        public ?string $postCode = null,
        public ?string $state = null,
        public ?string $countryCode = null,
        public ?string $building = null,
        public ?string $flat = null,
    ) {
    }
}
