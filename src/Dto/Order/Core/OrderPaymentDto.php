<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Pojedyncza płatność przypisana do zamówienia.
 */
final class OrderPaymentDto
{
    public function __construct(
        public ?string $paymentId = null,
        public ?string $paymentType = null,
        public ?\DateTimeInterface $paymentDate = null,
    ) {
    }
}
