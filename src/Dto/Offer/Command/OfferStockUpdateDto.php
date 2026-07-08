<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Command;

use malpka32\InPostBuySdk\Dto\Offer\StockDto;

/**
 * Single stock update entry for Batch Update Offer Stock.
 *
 * Payload item: { offerId, stock: { quantity, unit } }.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/patchOffersStocksV1
 */
final class OfferStockUpdateDto
{
    public function __construct(
        /** UUID oferty. */
        public string $offerId,
        /** Nowy stan magazynowy. */
        public StockDto $stock,
    ) {
    }

    /** @return array{offerId: string, stock: array{quantity: int, unit: string}} */
    public function toArray(): array
    {
        return [
            'offerId' => $this->offerId,
            'stock' => $this->stock->toArray(),
        ];
    }
}
