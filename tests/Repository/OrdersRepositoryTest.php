<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Dto\OrderStatusDto;
use malpka32\InPostBuySdk\Mapper\OrderResponseMapper;
use malpka32\InPostBuySdk\Repository\OrdersRepository;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use malpka32\InPostBuySdk\Tests\Fixtures\FakeOrdersEndpoint;
use PHPUnit\Framework\TestCase;

final class OrdersRepositoryTest extends TestCase
{
    public function testGetOrdersReturnsMappedCollection(): void
    {
        $data = ApiMocks::ordersListResponse();
        $endpoint = new FakeOrdersEndpoint(listResponse: $data);
        $repository = new OrdersRepository($endpoint, new OrderResponseMapper());

        $result = $repository->getOrders();

        $this->assertCount(1, $result);
        $this->assertSame('order-uuid-123', $result->offsetGet(0)->inpostOrderId);
    }

    public function testGetOrderReturnsDtoWhenFound(): void
    {
        $order = ApiMocks::singleOrderPayload();
        $endpoint = new FakeOrdersEndpoint(getResponse: $order);
        $repository = new OrdersRepository($endpoint, new OrderResponseMapper());

        $result = $repository->getOrder('order-id');

        $this->assertNotNull($result);
        $this->assertSame('order-uuid-123', $result->inpostOrderId);
    }

    public function testGetOrderReturnsNullWhen404(): void
    {
        $endpoint = new FakeOrdersEndpoint(getResponse: null);
        $repository = new OrdersRepository($endpoint, new OrderResponseMapper());

        $result = $repository->getOrder('missing');

        $this->assertNull($result);
    }

    public function testUpdateOrderStatusAccept(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderResponseMapper());

        $repository->updateOrderStatus('ord-1', new OrderStatusDto(status: 'accept'));
        $this->addToAssertionCount(1);
    }

    public function testUpdateOrderStatusRefuse(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderResponseMapper());

        $repository->updateOrderStatus('ord-1', new OrderStatusDto(status: 'refused', comment: 'Too expensive'));
        $this->addToAssertionCount(1);
    }
}
