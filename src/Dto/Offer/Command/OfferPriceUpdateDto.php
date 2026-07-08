<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Command;

use malpka32\InPostBuySdk\Dto\Offer\Core\MoneyDto;

/**
 * Single price update entry for Batch Update Offer Price.
 *
 * Payload item: { offerId, price: { amount, currency } }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/patchOffersPricesV1
 */
final class OfferPriceUpdateDto
{
    public function __construct(
        /** UUID oferty. */
        public string $offerId,
        /** Nowa cena (Money). */
        public MoneyDto $price,
    ) {
    }

    /** @return array{offerId: string, price: array{amount: float, currency: string}} */
    public function toArray(): array
    {
        return [
            'offerId' => $this->offerId,
            'price' => $this->price->toArray(),
        ];
    }
}
