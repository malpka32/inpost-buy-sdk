<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Amount with currency for order price fields.
 */
final class OrderMoneyDto
{
    public function __construct(
        public float $amount,
        public string $currency,
    ) {
    }
}
