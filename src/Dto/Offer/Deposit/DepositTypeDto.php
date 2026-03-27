<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Deposit;

/**
 * Reusable packaging deposit type (OpenAPI: DepositType).
 *
 * Used in PriceTag.deposits – deposit type identifier and price.
 * Available types: GET /v1/offers/deposit-types.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/getOffersDepositTypesV1
 */
final class DepositTypeDto
{
    public function __construct(
        /** Deposit type UUID from deposit-types API. */
        public string $id,
        /** Deposit amount. */
        public float $amount,
        /** Currency (e.g. PLN). */
        public string $currency,
    ) {
    }

    /** @return array{id: string, price: array{amount: float, currency: string}} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'price' => [
                'amount' => round($this->amount, 2),
                'currency' => $this->currency,
            ],
        ];
    }
}
