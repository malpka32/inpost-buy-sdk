<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Line;

/**
 * Order line item (OpenAPI: orderLines element).
 */
final class OrderLineDto
{
    public function __construct(
        public OrderLineOfferDto $offer,
    ) {
    }
}
