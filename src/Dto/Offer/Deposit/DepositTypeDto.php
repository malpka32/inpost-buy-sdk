<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Deposit;

/**
 * Typ kaucji za opakowanie wielokrotnego użytku (OpenAPI: DepositType).
 *
 * Używany w PriceTag.deposits – identyfikator typu kaucji oraz cena.
 * Dostępne typy: GET /v1/offers/deposit-types.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/getOffersDepositTypesV1
 */
final class DepositTypeDto
{
    public function __construct(
        /** UUID typu kaucji z API deposit-types. */
        public string $id,
        /** Kwota kaucji. */
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
