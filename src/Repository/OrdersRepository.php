<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\OrdersEndpointInterface;
use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Dto\Order\OrderEventType;
use malpka32\InPostBuySdk\Dto\Order\OrderPaymentStatus;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Order\OrderStatus;
use malpka32\InPostBuySdk\Dto\Order\Command\OrderCommandStatusDto;
use malpka32\InPostBuySdk\Dto\Order\OrderDto;
use malpka32\InPostBuySdk\Dto\Order\Response\OrderEventsResultDto;
use malpka32\InPostBuySdk\Dto\Order\OrderStatusDto;
use malpka32\InPostBuySdk\Mapper\Order\Core\OrderCollectionMapper;

/**
 * Orders repository – endpoint + mapping → DTO.
 */
final class OrdersRepository
{
    public function __construct(
        private readonly OrdersEndpointInterface $endpoint,
        private readonly OrderCollectionMapper $mapper,
    ) {
    }

    /**
     * @param list<ListSort|string>|null $sort
     */
    public function getOrders(
        OrderStatus|string|null $status = null,
        OrderPaymentStatus|string|null $paymentStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null,
    ): OrderCollection {
        $data = $this->endpoint->list($status, $paymentStatus, $limit, $offset, $sort);
        return $this->mapper->map($data);
    }

    public function getOrder(string $inpostOrderId): ?OrderDto
    {
        $data = $this->endpoint->get($inpostOrderId);
        if ($data === null) {
            return null;
        }
        return $this->mapper->mapItem($data);
    }

    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void
    {
        $lower = strtolower($status->status);
        if ($lower === 'accept' || $lower === 'accepted') {
            $this->endpoint->accept($inpostOrderId);
        } elseif ($lower === 'refuse' || $lower === 'refused') {
            $this->endpoint->refuse($inpostOrderId, $status->comment ?? '');
        }
    }

    public function getOrderCommandStatus(string $commandId): OrderCommandStatusDto
    {
        $data = $this->endpoint->getCommandStatus($commandId);
        return OrderCommandStatusDto::fromArray($data);
    }

    /**
     * @param list<OrderEventType|string>|null $eventType
     */
    public function getOrderEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): OrderEventsResultDto
    {
        $data = $this->endpoint->getEvents($untilId, $eventType, $limit);
        return OrderEventsResultDto::fromArray($data);
    }
}
