<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Pojedyncza paczka w sekcji dostawy.
 */
final class OrderDeliveryParcelDto
{
    public function __construct(
        public ?string $trackingNumber = null,
        public ?\DateTimeInterface $createdAt = null,
        public ?string $status = null,
    ) {
    }
}
