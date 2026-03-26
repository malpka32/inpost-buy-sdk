<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Dto\Order\OrderStatus;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentType;
use malpka32\InPostBuySdk\Mapper\Order\Core\OrderCollectionMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class OrderResponseMapperTest extends TestCase
{
    private OrderCollectionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new OrderCollectionMapper();
    }

    public function testMapEmptyResponseReturnsEmptyCollection(): void
    {
        $result = $this->mapper->map([]);
        $this->assertCount(0, $result);
    }

    public function testMapOrdersListFromOpenApiPayload(): void
    {
        $data = ApiMocks::ordersListResponse();
        $result = $this->mapper->map($data);

        $this->assertCount(1, $result);
        $order = $result->offsetGet(0);
        $this->assertSame('order-uuid-123', $order->inpostOrderId);
        $this->assertSame('org-uuid', $order->organizationId);
        $this->assertSame(OrderStatus::CREATED, $order->status);
        $this->assertSame('REF-001', $order->reference);
        $this->assertSame('Please ring the bell', $order->comment);
        $this->assertNotNull($order->createdAt);
        $this->assertNotNull($order->updatedAt);
        $this->assertNotNull($order->customer);
        $this->assertSame('buyer@example.com', $order->customer->email);
        $this->assertNotNull($order->customer->address);
        $this->assertSame('31-001', $order->customer->address->postCode);
        $this->assertNotNull($order->invoice);
        $this->assertSame('ACME Sp. z o.o.', $order->invoice->companyName);
        $this->assertNotNull($order->delivery);
        $this->assertSame('APM', $order->delivery->deliveryType);
        $this->assertNotNull($order->delivery->price);
        $this->assertSame(12.99, $order->delivery->price->amount);
        $this->assertNotNull($order->delivery->parcels);
        $this->assertCount(1, $order->delivery->parcels);
        $this->assertSame('PKG123456789', $order->delivery->parcels->offsetGet(0)->trackingNumber);
        $this->assertNotNull($order->orderLines);
        $this->assertCount(1, $order->orderLines);
        $line = $order->orderLines->offsetGet(0);
        $this->assertSame('offer-uuid-1', $line->offer->offerId);
        $this->assertNotNull($line->offer->product);
        $this->assertSame('Test product', $line->offer->product->name);
        $this->assertNotNull($line->offer->price);
        $this->assertSame(49.99, $line->offer->price->amount);
        $this->assertSame('PLN', $line->offer->price->currency);
        $this->assertNotNull($order->finalPrice);
        $this->assertSame(99.99, $order->finalPrice->amount);
        $this->assertNotNull($order->basePrice);
        $this->assertSame(109.99, $order->basePrice->amount);
        $this->assertNotNull($order->paymentDetails);
        $this->assertSame(OrderPaymentType::CARD, $order->paymentDetails->selectedPaymentType);
        $this->assertNotNull($order->paymentDetails->payments);
        $this->assertCount(1, $order->paymentDetails->payments);
        $this->assertSame('payment-1', $order->paymentDetails->payments->offsetGet(0)->paymentId);
        $this->assertSame(OrderPaymentType::CARD, $order->paymentDetails->payments->offsetGet(0)->paymentType);
    }

    public function testMapOrdersIgnoresUndocumentedOrdersKey(): void
    {
        $data = ['orders' => [ApiMocks::singleOrderPayload()]];
        $result = $this->mapper->map($data);
        $this->assertCount(0, $result);
    }

    public function testMapItemParsesIsoDateTime(): void
    {
        $item = ApiMocks::singleOrderPayload();
        $dto = $this->mapper->mapItem($item);
        $this->assertInstanceOf(\DateTimeInterface::class, $dto->createdAt);
        $this->assertInstanceOf(\DateTimeInterface::class, $dto->updatedAt);
    }

    public function testMapItemSupportsDocumentedCamelCase(): void
    {
        $item = [
            'id' => 'ord-1',
            'status' => 'ACCEPTED',
            'createdAt' => '2025-01-01T12:00:00+00:00',
            'updatedAt' => '2025-01-01T13:00:00+00:00',
            'delivery' => [],
            'orderLines' => [],
            'finalPrice' => [],
            'paymentDetails' => [],
        ];
        $dto = $this->mapper->mapItem($item);
        $this->assertSame('ord-1', $dto->inpostOrderId);
    }

    public function testMapItemIgnoresUndocumentedSnakeCaseKeys(): void
    {
        $line = ApiMocks::singleOrderPayload()['orderLines'][0];
        $item = [
            'id' => 'ord-snake',
            'order_lines' => [$line],
        ];
        $dto = $this->mapper->mapItem($item);
        $this->assertNull($dto->orderLines);
    }

    public function testMapItemIgnoresUndocumentedLegacyItemsKey(): void
    {
        $line = ApiMocks::singleOrderPayload()['orderLines'][0];
        $item = [
            'id' => 'ord-items-key',
            'items' => [$line],
        ];
        $dto = $this->mapper->mapItem($item);
        $this->assertNull($dto->orderLines);
    }
}
