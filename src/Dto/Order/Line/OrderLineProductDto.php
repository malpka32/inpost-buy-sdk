<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Line;

/**
 * Produkt na linii zamówienia (OpenAPI: product w obiekcie offer linii).
 */
final class OrderLineProductDto
{
    public function __construct(
        public ?string $productId = null,
        public ?string $name = null,
        public ?string $ean = null,
        public ?string $sku = null,
    ) {
    }
}
