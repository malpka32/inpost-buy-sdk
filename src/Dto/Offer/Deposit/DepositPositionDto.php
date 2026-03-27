<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Deposit;

/**
 * Deposit position in offer price (OpenAPI: DepositPosition).
 *
 * Number of packages covered by deposit and the deposit type.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class DepositPositionDto
{
    public function __construct(
        /** Number of packages covered by deposit. */
        public int $quantity,
        /** Deposit type (id + price). */
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
