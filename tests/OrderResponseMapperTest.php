<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Mapper\OrderResponseMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class OrderResponseMapperTest extends TestCase
{
    private OrderResponseMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new OrderResponseMapper();
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
        $this->assertSame('CREATED', $order->status);
        $this->assertSame('REF-001', $order->reference);
        $this->assertNotNull($order->createdAt);
        $this->assertNotNull($order->updatedAt);
    }

    public function testMapOrdersWithOrdersKey(): void
    {
        $data = ApiMocks::ordersListResponseWithOrdersKey();
        $result = $this->mapper->map($data);
        $this->assertCount(1, $result);
    }

    public function testMapItemParsesIsoDateTime(): void
    {
        $item = ApiMocks::singleOrderPayload();
        $dto = $this->mapper->mapItem($item);
        $this->assertInstanceOf(\DateTimeInterface::class, $dto->createdAt);
        $this->assertInstanceOf(\DateTimeInterface::class, $dto->updatedAt);
    }

    public function testMapItemSupportsSnakeAndCamelCase(): void
    {
        $item = [
            'order_id' => 'ord-1',
            'status' => 'ACCEPTED',
            'created_at' => '2025-01-01T12:00:00+00:00',
            'updated_at' => '2025-01-01T13:00:00+00:00',
            'delivery' => [],
            'orderLines' => [],
            'finalPrice' => [],
            'paymentDetails' => [],
        ];
        $dto = $this->mapper->mapItem($item);
        $this->assertSame('ord-1', $dto->inpostOrderId);
    }
}
