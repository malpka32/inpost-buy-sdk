<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Offer shipping time (OpenAPI: ShippingTime).
 *
 * Number of days the seller needs to ship the parcel.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class ShippingTimeDto
{
    public function __construct(
        /** Number of days to ship the parcel (min. 0). */
        public int $daysToShip,
    ) {
    }

    /** @return array{daysToShip: int} */
    public function toArray(): array
    {
        return ['daysToShip' => $this->daysToShip];
    }
}
