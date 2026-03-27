<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Line;

/**
 * Product within an order line (OpenAPI: product in the offer object of a line).
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
