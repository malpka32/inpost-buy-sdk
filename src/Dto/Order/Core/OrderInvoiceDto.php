<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order\Core;

/**
 * Invoice data associated with an order.
 */
final class OrderInvoiceDto
{
    public function __construct(
        public ?string $email = null,
        public ?string $legalForm = null,
        public ?string $companyName = null,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $taxIdPrefix = null,
        public ?string $taxId = null,
        public ?OrderAddressDto $address = null,
    ) {
    }
}
