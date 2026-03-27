<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Deposit;

/**
 * Deposit type label (OpenAPI: DepositLabel).
 *
 * Returned by GET /v1/offers/deposit-types.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#operation/getOffersDepositTypesV1
 */
final class DepositLabelDto
{
    public function __construct(
        /** Deposit type name (e.g. "Reusable glass bottles"). */
        public string $name,
        /** Deposit type with id and price. */
        public DepositTypeDto $depositType,
    ) {
    }
}
