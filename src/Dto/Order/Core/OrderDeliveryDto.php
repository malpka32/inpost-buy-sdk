<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

use malpka32\InPostBuySdk\Collection\OrderDeliveryParcelCollection;

/**
 * Order delivery data.
 */
final class OrderDeliveryDto
{
    public function __construct(
        public ?string $deliveryType = null,
        public ?OrderDeliveryParcelCollection $parcels = null,
        public ?string $name = null,
        public ?string $deliveryPoint = null,
        public ?OrderAddressDto $address = null,
        public ?string $email = null,
        public ?string $phoneNumber = null,
        public ?OrderMoneyDto $price = null,
        public ?\DateTimeInterface $expectedDeliveryDate = null,
    ) {
    }
}
