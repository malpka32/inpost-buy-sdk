<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Order;

use malpka32\InPostBuySdk\Collection\OrderLineCollection;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderCustomerDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderDeliveryDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderInvoiceDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderMoneyDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderPaymentDetailsDto;

/**
 * Order DTO from InPost Buy API.
 */
final class OrderDto
{
    public function __construct(
        public string $inpostOrderId,
        public ?string $organizationId = null,
        public ?OrderStatus $status = null,
        public ?string $reference = null,
        public ?\DateTimeInterface $createdAt = null,
        public ?\DateTimeInterface $updatedAt = null,
        public ?OrderCustomerDto $customer = null,
        public ?OrderInvoiceDto $invoice = null,
        public ?OrderDeliveryDto $delivery = null,
        /** Order line items (OpenAPI: orderLines); null when key is missing from response. */
        public ?OrderLineCollection $orderLines = null,
        public ?OrderMoneyDto $finalPrice = null,
        public ?OrderMoneyDto $basePrice = null,
        public ?OrderMoneyDto $promotionPrice = null,
        public ?OrderPaymentDetailsDto $paymentDetails = null,
        public ?string $comment = null,
        /** @var array<string, mixed>|null */
        public ?array $raw = null,
    ) {
    }
}
