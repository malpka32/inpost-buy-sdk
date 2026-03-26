<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Dto\Order\OrderStatusDto;
use malpka32\InPostBuySdk\Dto\Order\OrderEventType;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentStatus;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;
use malpka32\InPostBuySdk\Dto\Order\OrderUpdateStatus;
use malpka32\InPostBuySdk\Mapper\Order\Core\OrderCollectionMapper;
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
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $result = $repository->getOrders();

        $this->assertCount(1, $result);
        $this->assertSame('order-uuid-123', $result->offsetGet(0)->inpostOrderId);
    }

    public function testGetOrdersPassesAllFiltersToEndpoint(): void
    {
        $data = ApiMocks::ordersListResponse();
        $endpoint = new FakeOrdersEndpoint(listResponse: $data);
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $repository->getOrders(
            status: OrderStatus::CREATED,
            paymentStatus: OrderPaymentStatus::PAID,
            limit: 20,
            offset: 40,
            sort: [ListSort::CREATED_AT_DESC, ListSort::STATUS_ASC],
        );

        $this->assertNotNull($endpoint->lastListCall);
        $this->assertSame(OrderStatus::CREATED, $endpoint->lastListCall['status']);
        $this->assertSame(OrderPaymentStatus::PAID, $endpoint->lastListCall['paymentStatus']);
        $this->assertSame(20, $endpoint->lastListCall['limit']);
        $this->assertSame(40, $endpoint->lastListCall['offset']);
        $this->assertSame([ListSort::CREATED_AT_DESC, ListSort::STATUS_ASC], $endpoint->lastListCall['sort']);
    }

    public function testGetOrderReturnsDtoWhenFound(): void
    {
        $order = ApiMocks::singleOrderPayload();
        $endpoint = new FakeOrdersEndpoint(getResponse: $order);
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $result = $repository->getOrder('order-id');

        $this->assertNotNull($result);
        $this->assertSame('order-uuid-123', $result->inpostOrderId);
    }

    public function testGetOrderReturnsNullWhen404(): void
    {
        $endpoint = new FakeOrdersEndpoint(getResponse: null);
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $result = $repository->getOrder('missing');

        $this->assertNull($result);
    }

    public function testUpdateOrderStatusAccept(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $repository->updateOrderStatus('ord-1', new OrderStatusDto(status: 'accept'));
        $this->addToAssertionCount(1);
    }

    public function testUpdateOrderStatusRefuse(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $repository->updateOrderStatus('ord-1', new OrderStatusDto(status: 'refused', comment: 'Too expensive'));
        $this->addToAssertionCount(1);
    }

    public function testUpdateOrderStatusAcceptWithEnum(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $repository->updateOrderStatus('ord-1', new OrderStatusDto(status: OrderUpdateStatus::ACCEPTED));
        $this->addToAssertionCount(1);
    }

    public function testGetOrderCommandStatusReturnsDto(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $result = $repository->getOrderCommandStatus('cmd-123');

        $this->assertSame('cmd-123', $result->commandId);
        $this->assertSame('COMPLETED', $result->status);
    }

    public function testGetOrderEventsReturnsResultDto(): void
    {
        $endpoint = new FakeOrdersEndpoint(eventsResponse: ApiMocks::orderEventsResponse());
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $result = $repository->getOrderEvents(limit: 50);

        $events = $result->getEvents();
        $this->assertCount(1, $events);

        $event = $events->offsetGet(0);
        $this->assertSame('evt-uuid-1', $event->id);
        $this->assertNotNull($event->order);
        $this->assertSame('order-uuid-123', $event->order->id);
        $this->assertSame(OrderEventType::CREATED, $event->eventType);
        $this->assertInstanceOf(\DateTimeInterface::class, $event->occurredAt);
    }

    public function testGetOrderEventsAcceptsEnumEventTypes(): void
    {
        $endpoint = new FakeOrdersEndpoint();
        $repository = new OrdersRepository($endpoint, new OrderCollectionMapper());

        $result = $repository->getOrderEvents(
            eventType: [OrderEventType::CREATED, OrderEventType::PAID],
            limit: 50,
        );

        $this->assertCount(0, $result->getEvents());
    }
}
