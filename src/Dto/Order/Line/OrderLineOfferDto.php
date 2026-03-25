<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Line;

use malpka32\InPostBuySdk\Dto\Order\Core\OrderMoneyDto;

/**
 * Oferta na linii zamówienia (OpenAPI: offer w orderLines[]).
 */
final class OrderLineOfferDto
{
    public function __construct(
        public ?string $offerId = null,
        public ?OrderLineProductDto $product = null,
        public ?OrderMoneyDto $price = null,
        public ?OrderMoneyDto $basePrice = null,
        public ?OrderMoneyDto $promotionPrice = null,
        public ?string $externalId = null,
    ) {
    }
}
