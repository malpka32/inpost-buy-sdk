<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Czas wysyłki oferty (OpenAPI: ShippingTime).
 *
 * Liczba dni potrzebnych sprzedawcy na wysłanie paczki.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class ShippingTimeDto
{
    public function __construct(
        /** Liczba dni na wysłanie paczki (min. 0). */
        public int $daysToShip,
    ) {
    }

    /** @return array{daysToShip: int} */
    public function toArray(): array
    {
        return ['daysToShip' => $this->daysToShip];
    }
}
