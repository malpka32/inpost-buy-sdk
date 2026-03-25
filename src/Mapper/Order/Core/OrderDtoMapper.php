<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Mapper\Order\Core;

use malpka32\InPostBuySdk\Dto\Order\OrderDto;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderCustomerDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderDeliveryDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderInvoiceDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderMoneyDto;
use malpka32\InPostBuySdk\Dto\Order\Core\OrderPaymentDetailsDto;
use malpka32\InPostBuySdk\Helper\ArrayHelper;
use malpka32\InPostBuySdk\Mapper\ItemMapperInterface;
use malpka32\InPostBuySdk\Mapper\Order\Line\OrderLineCollectionMapper;

/**
 * @implements ItemMapperInterface<OrderDto>
 */
final class OrderDtoMapper implements ItemMapperInterface
{
    public function __construct(
        private readonly OrderLineCollectionMapper $orderLineCollectionMapper = new OrderLineCollectionMapper(),
        private readonly OrderCustomerDtoMapper $customerMapper = new OrderCustomerDtoMapper(),
        private readonly OrderInvoiceDtoMapper $invoiceMapper = new OrderInvoiceDtoMapper(),
        private readonly OrderDeliveryDtoMapper $deliveryMapper = new OrderDeliveryDtoMapper(),
        private readonly OrderMoneyDtoMapper $moneyMapper = new OrderMoneyDtoMapper(),
        private readonly OrderPaymentDetailsDtoMapper $paymentDetailsMapper = new OrderPaymentDetailsDtoMapper(),
    ) {
    }

    public function canProcess(array $item): bool
    {
        return array_key_exists('id', $item);
    }

    public function mapItem(mixed $item): OrderDto
    {
        $item = is_array($item) ? $item : [];
        /** @var array<string, mixed> $item */
        $createdAt = self::parseDateTime(ArrayHelper::get($item, 'createdAt'));
        $updatedAt = self::parseDateTime(ArrayHelper::get($item, 'updatedAt'));

        $status = ArrayHelper::get($item, 'status');
        $organizationId = ArrayHelper::get($item, 'organizationId');
        $reference = ArrayHelper::get($item, 'reference');
        $customer = $this->mapCustomer(ArrayHelper::get($item, 'customer'));
        $invoice = $this->mapInvoice(ArrayHelper::get($item, 'invoice'));
        $delivery = $this->mapDelivery(ArrayHelper::get($item, 'delivery'));
        $linesRaw = ArrayHelper::get($item, 'orderLines');
        $finalPrice = $this->mapMoney(ArrayHelper::get($item, 'finalPrice'));
        $basePrice = $this->mapMoney(ArrayHelper::get($item, 'basePrice'));
        $promotionPrice = $this->mapMoney(ArrayHelper::get($item, 'promotionPrice'));
        $paymentDetails = $this->mapPaymentDetails(ArrayHelper::get($item, 'paymentDetails'));
        $comment = ArrayHelper::get($item, 'comment');
        $orderLines = null;
        if (is_array($linesRaw)) {
            $filtered = array_values(array_filter($linesRaw, 'is_array'));
            /** @var list<array<string, mixed>> $filtered */
            $orderLines = $this->orderLineCollectionMapper->map($filtered);
        }

        return new OrderDto(
            inpostOrderId: ArrayHelper::asString(ArrayHelper::get($item, 'id', '')),
            organizationId: $organizationId === null ? null : ArrayHelper::asString($organizationId),
            status: $status === null ? null : OrderStatus::tryFrom(ArrayHelper::asString($status)),
            reference: $reference === null ? null : ArrayHelper::asString($reference),
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            customer: $customer,
            invoice: $invoice,
            delivery: $delivery,
            orderLines: $orderLines,
            finalPrice: $finalPrice,
            basePrice: $basePrice,
            promotionPrice: $promotionPrice,
            paymentDetails: $paymentDetails,
            comment: $comment === null ? null : ArrayHelper::asString($comment),
            raw: $item,
        );
    }

    private function mapCustomer(mixed $raw): ?OrderCustomerDto
    {
        $mapped = $this->customerMapper->map($raw);
        return $mapped instanceof OrderCustomerDto ? $mapped : null;
    }

    private function mapInvoice(mixed $raw): ?OrderInvoiceDto
    {
        $mapped = $this->invoiceMapper->map($raw);
        return $mapped instanceof OrderInvoiceDto ? $mapped : null;
    }

    private function mapDelivery(mixed $raw): ?OrderDeliveryDto
    {
        $mapped = $this->deliveryMapper->map($raw);
        return $mapped instanceof OrderDeliveryDto ? $mapped : null;
    }

    private function mapMoney(mixed $raw): ?OrderMoneyDto
    {
        $mapped = $this->moneyMapper->map($raw);
        return $mapped instanceof OrderMoneyDto ? $mapped : null;
    }

    private function mapPaymentDetails(mixed $raw): ?OrderPaymentDetailsDto
    {
        $mapped = $this->paymentDetailsMapper->map($raw);
        return $mapped instanceof OrderPaymentDetailsDto ? $mapped : null;
    }

    private static function parseDateTime(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = ArrayHelper::asString($value);
        $parsed = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $str);
        if ($parsed instanceof \DateTimeInterface) {
            return $parsed;
        }
        try {
            return new \DateTimeImmutable($str);
        } catch (\Exception) {
            return null;
        }
    }
}
