<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\OrderPaymentType;

/**
 * Pojedyncza płatność przypisana do zamówienia.
 */
final class OrderPaymentDto
{
    public function __construct(
        public ?string $paymentId = null,
        public OrderPaymentType|string|null $paymentType = null,
        public ?\DateTimeInterface $paymentDate = null,
    ) {
    }
}
