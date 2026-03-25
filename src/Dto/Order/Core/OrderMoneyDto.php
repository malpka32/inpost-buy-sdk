<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Kwota z walutą dla pól cenowych zamówienia.
 */
final class OrderMoneyDto
{
    public function __construct(
        public float $amount,
        public string $currency,
    ) {
    }
}
