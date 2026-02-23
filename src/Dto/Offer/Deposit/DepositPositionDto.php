<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Deposit;

/**
 * Pozycja kaucji w cenie oferty (OpenAPI: DepositPosition).
 *
 * Ilość sztuk opakowań objętych kaucją oraz typ kaucji.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class DepositPositionDto
{
    public function __construct(
        /** Liczba sztuk opakowań objętych kaucją. */
        public int $quantity,
        /** Typ kaucji (id + cena). */
        public DepositTypeDto $depositType,
    ) {
    }

    /** @return array{quantity: int, depositType: array{id: string, price: array{amount: float, currency: string}}} */
    public function toArray(): array
    {
        return [
            'quantity' => $this->quantity,
            'depositType' => $this->depositType->toArray(),
        ];
    }
}
