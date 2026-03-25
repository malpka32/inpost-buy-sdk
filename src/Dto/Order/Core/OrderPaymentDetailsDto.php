<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

use malpka32\InPostBuySdk\Collection\OrderPaymentCollection;

/**
 * Szczegóły płatności zamówienia.
 */
final class OrderPaymentDetailsDto
{
    public function __construct(
        public ?string $selectedPaymentType = null,
        public ?OrderPaymentCollection $payments = null,
    ) {
    }
}
