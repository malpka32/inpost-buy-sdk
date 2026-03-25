<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Line;

/**
 * Pozycja zamówienia (OpenAPI: element orderLines).
 */
final class OrderLineDto
{
    public function __construct(
        public OrderLineOfferDto $offer,
    ) {
    }
}
